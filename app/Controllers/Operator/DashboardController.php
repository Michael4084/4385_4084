<?php

namespace App\Controllers\Operator;

use App\Models\OperationTypeModel;
use App\Models\OperatorPrefixModel;
use App\Models\TransactionModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $transactionModel = new TransactionModel();
        $operationTypeModel = new OperationTypeModel();
        $prefixModel = new OperatorPrefixModel();
        $db = \Config\Database::connect();

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $typeCode = $this->request->getGet('type_code');

        $builder = $db->table('transactions t')
            ->select(' 
                COUNT(t.id) as total_transactions,
                SUM(t.amount) as total_volume,
                SUM(t.fee_amount) as total_fees,
                SUM(CASE WHEN o.code = "DEPOSIT" THEN 1 ELSE 0 END) as count_deposits,
                SUM(CASE WHEN o.code = "WITHDRAWAL" THEN 1 ELSE 0 END) as count_withdrawals,
                SUM(CASE WHEN o.code = "TRANSFER" THEN 1 ELSE 0 END) as count_transfers,
                SUM(CASE WHEN o.code = "WITHDRAWAL" THEN t.fee_amount ELSE 0 END) as fee_withdrawals,
                SUM(CASE WHEN o.code = "TRANSFER" THEN t.fee_amount ELSE 0 END) as fee_transfers
            ')
            ->join('operation_types o', 'o.id = t.operation_type_id');

        if ($startDate) $builder->where('t.created_at >=', $startDate . ' 00:00:00');
        if ($endDate) $builder->where('t.created_at <=', $endDate . ' 23:59:59');
        if ($typeCode) $builder->where('o.code', $typeCode);

        $stats = $builder->get()->getRowArray();

        $transactions = $db->table('transactions t')
            ->select('t.*, o.code, r.phone_number as receiver_phone')
            ->join('operation_types o', 'o.id = t.operation_type_id')
            ->join('clients r', 'r.id = t.receiver_client_id', 'left');
        if ($startDate) $transactions->where('t.created_at >=', $startDate . ' 00:00:00');
        if ($endDate) $transactions->where('t.created_at <=', $endDate . ' 23:59:59');
        if ($typeCode) $transactions->where('o.code', $typeCode);
        $transactions = $transactions->get()->getResultArray();

        $ownOperatorFees = 0.0;
        $otherOperatorFees = 0.0;
        $operatorBreakdown = [];
        $ownPrefixes = array_column($prefixModel->where('is_active', 1)->findAll(), 'prefix');

        foreach ($transactions as $transaction) {
            if (($transaction['code'] ?? '') !== 'TRANSFER') {
                continue;
            }

            $prefix = $this->extractPrefix($transaction['receiver_phone'] ?? '');
            if ($prefix && in_array($prefix, $ownPrefixes, true)) {
                $ownOperatorFees += (float) ($transaction['fee_amount'] ?? 0.0);
            } else {
                $otherOperatorFees += (float) ($transaction['inter_operator_commission'] ?? 0.0);
                $operatorBreakdown[$prefix ?: 'autre'] = ($operatorBreakdown[$prefix ?: 'autre'] ?? 0.0) + (float) ($transaction['inter_operator_commission'] ?? 0.0);
            }
        }

        $operationTypes = $operationTypeModel->findAll();

        return view('operator/dashboard', [
            'stats' => $stats,
            'operationTypes' => $operationTypes,
            'ownOperatorFees' => $ownOperatorFees,
            'otherOperatorFees' => $otherOperatorFees,
            'operatorBreakdown' => $operatorBreakdown,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type_code' => $typeCode
            ]
        ]);
    }

    private function extractPrefix(?string $phoneNumber): ?string
    {
        if (!$phoneNumber) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phoneNumber);
        return strlen($digits) >= 3 ? substr($digits, 0, 3) : null;
    }
}
