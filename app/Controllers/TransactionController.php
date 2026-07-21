<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Services\TransactionService;
use CodeIgniter\Controller;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }

    public function deposit()
    {
        $clientModel = new ClientModel();
        $client = $clientModel->find(session()->get('client_id'));
        return view('client/deposit', ['client' => $client]);
    }

    public function processDeposit()
    {
        $amount = (float) $this->request->getPost('amount');
        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à zéro.');
        }

        $clientId = session()->get('client_id');
        $result = $this->transactionService->deposit($clientId, $amount);

        if ($result['success']) {
            return redirect()->to('/client/dashboard')->with('success', "Dépôt de {$amount} Ar réussi. Frais : {$result['fee']} Ar. Nouveau solde : {$result['balance_after']} Ar. Réf: {$result['reference']}");
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function withdraw()
    {
        $clientModel = new ClientModel();
        $client = $clientModel->find(session()->get('client_id'));
        return view('client/withdraw', ['client' => $client]);
    }

    public function processWithdraw()
    {
        $amount = (float) $this->request->getPost('amount');
        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à zéro.');
        }

        $clientId = session()->get('client_id');
        $result = $this->transactionService->withdraw($clientId, $amount);

        if ($result['success']) {
            return redirect()->to('/client/dashboard')->with('success', "Retrait de {$amount} Ar réussi. Frais : {$result['fee']} Ar. Coût total : {$result['total']} Ar. Nouveau solde : {$result['balance_after']} Ar. Réf: {$result['reference']}");
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function transfer()
    {
        $clientModel = new ClientModel();
        $client = $clientModel->find(session()->get('client_id'));
        return view('client/transfer', ['client' => $client]);
    }

    public function processTransfer()
    {
        $amount = (float) $this->request->getPost('amount');
        $receiverInput = $this->request->getPost('receiver_phone_numbers') ?: $this->request->getPost('receiver_phone_number');
        $includeWithdrawalFee = (bool) $this->request->getPost('include_withdrawal_fee');

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à zéro.');
        }
        if (!$receiverInput) {
            return redirect()->back()->with('error', 'Le numéro du destinataire est obligatoire.');
        }

        $receivers = preg_split('/[\r\n,;]+/', (string) $receiverInput);
        $receivers = array_values(array_filter(array_map('trim', $receivers)));
        if ($receivers === []) {
            return redirect()->back()->with('error', 'Aucun destinataire valide n’a été fourni.');
        }

        $clientId = session()->get('client_id');
        $result = $this->transactionService->transfer($clientId, $receivers, $amount, $includeWithdrawalFee);

        if ($result['success']) {
            $destinationLabel = count($receivers) > 1 ? 'plusieurs destinataires' : $receivers[0];
            $extraMessage = $includeWithdrawalFee ? ' avec frais de retrait inclus' : '';
            return redirect()->to('/client/dashboard')->with('success', "Transfert de {$amount} Ar vers {$destinationLabel} réussi{$extraMessage}. Frais : {$result['fee']} Ar. Coût total : {$result['total']} Ar. Nouveau solde : {$result['balance_after']} Ar. Réf: {$result['reference']}");
        } else {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }
    }
}
