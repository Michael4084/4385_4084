<?php

namespace App\Services;

use App\Models\ClientModel;
use App\Models\FeeBracketModel;
use App\Models\OperationTypeModel;
use App\Models\OperatorCommissionModel;
use App\Models\OperatorModel;
use App\Models\OperatorPrefixModel;
use App\Models\TransactionModel;
use App\Models\TransactionRecipientModel;

class TransactionService
{
    protected ClientModel $clientModel;
    protected FeeBracketModel $feeBracketModel;
    protected OperationTypeModel $operationTypeModel;
    protected OperatorCommissionModel $operatorCommissionModel;
    protected OperatorModel $operatorModel;
    protected OperatorPrefixModel $operatorPrefixModel;
    protected TransactionModel $transactionModel;
    protected TransactionRecipientModel $transactionRecipientModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->feeBracketModel = new FeeBracketModel();
        $this->operationTypeModel = new OperationTypeModel();
        $this->operatorCommissionModel = new OperatorCommissionModel();
        $this->operatorModel = new OperatorModel();
        $this->operatorPrefixModel = new OperatorPrefixModel();
        $this->transactionModel = new TransactionModel();
        $this->transactionRecipientModel = new TransactionRecipientModel();
    }

    public function deposit(int $clientId, float $amount): array
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return ['success' => false, 'message' => 'Compte client introuvable.'];
        }
        if ($client['status'] !== 'active') {
            return ['success' => false, 'message' => 'Compte suspendu.'];
        }

        $fee = $this->getFeeAmount('DEPOSIT', $amount);
        $total = $amount;
        $balanceBefore = (float) $client['balance'];
        $balanceAfter = $balanceBefore + $amount;

        $transactionId = $this->createTransaction(
            operationCode: 'DEPOSIT',
            senderClientId: null,
            receiverClientId: $clientId,
            senderOperatorId: null,
            receiverOperatorId: null,
            amount: $amount,
            feeAmount: $fee,
            totalAmount: $total,
            balanceBefore: $balanceBefore,
            balanceAfter: $balanceAfter,
            includeWithdrawalFee: 0,
            interOperatorCommission: 0.00,
        );

        if (!$transactionId) {
            return ['success' => false, 'message' => 'Échec de la création de la transaction.'];
        }

        $this->clientModel->update($clientId, ['balance' => $balanceAfter]);

        return [
            'success' => true,
            'fee' => $fee,
            'total' => $total,
            'balance_after' => $balanceAfter,
            'reference' => $this->getReference($transactionId),
        ];
    }

    public function withdraw(int $clientId, float $amount): array
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return ['success' => false, 'message' => 'Compte client introuvable.'];
        }
        if ($client['status'] !== 'active') {
            return ['success' => false, 'message' => 'Compte suspendu.'];
        }

        $fee = $this->getFeeAmount('WITHDRAWAL', $amount);
        $total = $amount + $fee;
        $balanceBefore = (float) $client['balance'];
        if ($balanceBefore < $total) {
            return ['success' => false, 'message' => 'Solde insuffisant pour ce retrait.'];
        }
        $balanceAfter = $balanceBefore - $total;

        $transactionId = $this->createTransaction(
            operationCode: 'WITHDRAWAL',
            senderClientId: $clientId,
            receiverClientId: null,
            senderOperatorId: null,
            receiverOperatorId: null,
            amount: $amount,
            feeAmount: $fee,
            totalAmount: $total,
            balanceBefore: $balanceBefore,
            balanceAfter: $balanceAfter,
            includeWithdrawalFee: 0,
            interOperatorCommission: 0.00,
        );

        if (!$transactionId) {
            return ['success' => false, 'message' => 'Échec de la création de la transaction.'];
        }

        $this->clientModel->update($clientId, ['balance' => $balanceAfter]);

        return [
            'success' => true,
            'fee' => $fee,
            'total' => $total,
            'balance_after' => $balanceAfter,
            'reference' => $this->getReference($transactionId),
        ];
    }

    public function transfer(int $clientId, array|string $receiverPhones, float $amount, bool $includeWithdrawalFee = false): array
    {
        $sender = $this->clientModel->find($clientId);
        if (!$sender) {
            return ['success' => false, 'message' => 'Compte expéditeur introuvable.'];
        }
        if ($sender['status'] !== 'active') {
            return ['success' => false, 'message' => 'Compte suspendu.'];
        }

        $receiverList = is_array($receiverPhones) ? $receiverPhones : [$receiverPhones];
        $receiverList = array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $receiverList)));
        if ($receiverList === []) {
            return ['success' => false, 'message' => 'Aucun destinataire n’a été fourni.'];
        }

        $currentOperator = $this->operatorModel->orderBy('id', 'ASC')->first();
        $currentOperatorId = $currentOperator['id'] ?? null;
        $currentOperatorPrefixes = $this->getOperatorPrefixes($currentOperatorId);

        $receiverData = [];
        $receiverOperatorIds = [];
        foreach ($receiverList as $phone) {
            $phone = $this->normalizePhoneNumber($phone);
            if (!$this->isValidPhoneNumber($phone)) {
                return ['success' => false, 'message' => 'Le numéro du destinataire est invalide.'];
            }

            $receiver = $this->clientModel->where('phone_number', $phone)->first();
            if (!$receiver) {
                $receiverId = $this->clientModel->insert([
                    'phone_number' => $phone,
                    'balance' => 0.00,
                    'status' => 'active',
                ]);
                $receiver = $this->clientModel->find($receiverId);
            }

            if ($receiver['status'] !== 'active') {
                return ['success' => false, 'message' => 'Le compte destinataire est suspendu.'];
            }

            $receiverData[] = ['phone' => $phone, 'id' => $receiver['id']];
            $receiverOperatorIds[] = $this->resolveOperatorIdByPhone($phone);
        }

        $firstReceiverOperatorId = $receiverOperatorIds[0] ?? null;
        $sameOperatorRecipients = count(array_unique($receiverOperatorIds)) === 1;
        if (count($receiverList) > 1 && !$sameOperatorRecipients) {
            return ['success' => false, 'message' => 'Les transferts multiples doivent être vers le même opérateur.'];
        }

        $isInterOperator = false;
        if ($currentOperatorId && $firstReceiverOperatorId && $currentOperatorId !== $firstReceiverOperatorId) {
            $isInterOperator = true;
        }

        if (count($receiverList) > 1 && $isInterOperator) {
            return ['success' => false, 'message' => 'Les transferts multiples sont réservés aux numéros du même opérateur.'];
        }

        $transferFee = $this->getFeeAmount('TRANSFER', $amount);
        $commissionPercentage = $this->getOperatorCommissionPercentage($currentOperatorId, 'TRANSFER', $amount);
        $interOperatorCommission = $isInterOperator ? round(($amount * $commissionPercentage) / 100, 2) : 0.0;
        $withdrawalFee = (!$isInterOperator && $includeWithdrawalFee) ? $this->getFeeAmount('WITHDRAWAL', $amount) : 0.0;
        $totalAmount = $amount + $transferFee + $interOperatorCommission + $withdrawalFee;

        $balanceBefore = (float) $sender['balance'];
        if ($balanceBefore < $totalAmount) {
            return ['success' => false, 'message' => 'Solde insuffisant pour ce transfert.'];
        }

        $balanceAfterSender = $balanceBefore - $totalAmount;
        $recipientAmounts = $this->splitAmount($amount, count($receiverList));

        $transactionId = $this->createTransaction(
            operationCode: 'TRANSFER',
            senderClientId: $clientId,
            receiverClientId: $receiverData[0]['id'],
            senderOperatorId: $currentOperatorId,
            receiverOperatorId: $firstReceiverOperatorId,
            amount: $amount,
            feeAmount: $transferFee + $withdrawalFee,
            totalAmount: $totalAmount,
            balanceBefore: $balanceBefore,
            balanceAfter: $balanceAfterSender,
            includeWithdrawalFee: $includeWithdrawalFee ? 1 : 0,
            interOperatorCommission: $interOperatorCommission,
        );

        if (!$transactionId) {
            return ['success' => false, 'message' => 'Échec de la création de la transaction.'];
        }

        foreach ($receiverData as $index => $receiverItem) {
            $recipientAmount = $recipientAmounts[$index] ?? 0.0;
            $this->transactionRecipientModel->insert([
                'transaction_id' => $transactionId,
                'receiver_phone_number' => $receiverItem['phone'],
                'amount' => $recipientAmount,
            ]);

            $receiverBalance = (float) $this->clientModel->find($receiverItem['id'])['balance'];
            $this->clientModel->update($receiverItem['id'], ['balance' => $receiverBalance + $recipientAmount]);
        }

        $this->clientModel->update($clientId, ['balance' => $balanceAfterSender]);

        return [
            'success' => true,
            'fee' => $transferFee + $withdrawalFee,
            'commission' => $interOperatorCommission,
            'total' => $totalAmount,
            'balance_after' => $balanceAfterSender,
            'reference' => $this->getReference($transactionId),
        ];
    }

    protected function getFeeAmount(string $operationCode, float $amount): float
    {
        $operationType = $this->operationTypeModel->where('code', $operationCode)->first();
        if (!$operationType) {
            return 0.0;
        }

        $bracket = $this->feeBracketModel
            ->where('operation_type_id', $operationType['id'])
            ->where('min_amount <=', $amount)
            ->where('max_amount >=', $amount)
            ->orderBy('min_amount', 'ASC')
            ->first();

        return (float) ($bracket['fee_amount'] ?? 0.0);
    }

    protected function getOperatorCommissionPercentage(?int $operatorId, string $operationCode, float $amount): float
    {
        if (!$operatorId) {
            return 0.0;
        }

        $operationType = $this->operationTypeModel->where('code', $operationCode)->first();
        if (!$operationType) {
            return 0.0;
        }

        $commissions = $this->operatorCommissionModel
            ->where('operator_id', $operatorId)
            ->where('operation_type_id', $operationType['id'])
            ->orderBy('min_amount', 'ASC')
            ->findAll();

        foreach ($commissions as $commission) {
            $minAmount = (float) ($commission['min_amount'] ?? 0.0);
            $maxAmount = $commission['max_amount'];
            $isInRange = $amount >= $minAmount && ($maxAmount === null || $amount <= (float) $maxAmount);
            if ($isInRange) {
                return (float) ($commission['commission_percentage'] ?? 0.0);
            }
        }

        return 0.0;
    }

    protected function getOperatorPrefixes(?int $operatorId): array
    {
        if (!$operatorId) {
            return [];
        }

        return array_column(
            $this->operatorPrefixModel->where('operator_id', $operatorId)->where('is_active', 1)->findAll(),
            'prefix'
        );
    }

    protected function resolveOperatorIdByPhone(string $phoneNumber): ?int
    {
        $prefix = $this->getPhonePrefix($phoneNumber);
        if ($prefix === null) {
            return null;
        }

        $prefixConfig = $this->operatorPrefixModel->where('prefix', $prefix)->where('is_active', 1)->first();
        return $prefixConfig['operator_id'] ?? null;
    }

    protected function getPhonePrefix(string $phoneNumber): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (strlen($digits) < 3) {
            return null;
        }

        return substr($digits, 0, 3);
    }

    protected function normalizePhoneNumber(string $phoneNumber): string
    {
        return preg_replace('/[^0-9]/', '', $phoneNumber);
    }

    protected function isValidPhoneNumber(string $phoneNumber): bool
    {
        return preg_match('/^[0-9]{10}$/', $phoneNumber) === 1;
    }

    protected function splitAmount(float $amount, int $count): array
    {
        if ($count <= 1) {
            return [$amount];
        }

        $parts = [];
        $remaining = $amount;
        for ($i = 0; $i < $count; $i++) {
            $share = $i === $count - 1 ? round($remaining, 2) : round($remaining / ($count - $i), 2);
            $parts[] = $share;
            $remaining = round($remaining - $share, 2);
        }

        return $parts;
    }

    protected function createTransaction(
        string $operationCode,
        ?int $senderClientId,
        ?int $receiverClientId,
        ?int $senderOperatorId,
        ?int $receiverOperatorId,
        float $amount,
        float $feeAmount,
        float $totalAmount,
        float $balanceBefore,
        float $balanceAfter,
        int $includeWithdrawalFee,
        float $interOperatorCommission,
    ): int {
        $operationType = $this->operationTypeModel->where('code', $operationCode)->first();
        if (!$operationType) {
            throw new \RuntimeException('Type d\'opération introuvable.');
        }

        $transactionId = $this->transactionModel->insert([
            'transaction_reference' => $this->generateReference(),
            'operation_type_id' => $operationType['id'],
            'sender_client_id' => $senderClientId,
            'receiver_client_id' => $receiverClientId,
            'sender_operator_id' => $senderOperatorId,
            'receiver_operator_id' => $receiverOperatorId,
            'amount' => $amount,
            'fee_amount' => $feeAmount,
            'inter_operator_commission' => $interOperatorCommission,
            'total_amount' => $totalAmount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'include_withdrawal_fee' => $includeWithdrawalFee,
            'status' => 'completed',
        ]);

        return (int) $transactionId;
    }

    protected function generateReference(): string
    {
        do {
            $reference = 'TRX-' . strtoupper(bin2hex(random_bytes(4)));
        } while ($this->transactionModel->where('transaction_reference', $reference)->first());

        return $reference;
    }

    protected function getReference(int $id): string
    {
        return $this->transactionModel->find($id)['transaction_reference'] ?? '';
    }
}
