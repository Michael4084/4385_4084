<?php

namespace App\Controllers\Operator;

use App\Models\ClientModel;
use App\Models\TransactionModel;
use CodeIgniter\Controller;

class ClientController extends Controller
{
    public function index()
    {
        $clientModel = new ClientModel();
        
        $search = $this->request->getGet('search');
        
        if ($search) {
            $clientModel->like('phone_number', $search);
        }

        $clients = $clientModel->orderBy('created_at', 'DESC')->paginate(20);

        return view('operator/clients/index', [
            'clients' => $clients,
            'pager'   => $clientModel->pager,
            'search'  => $search
        ]);
    }

    public function show($id)
    {
        $clientModel = new ClientModel();
        $transactionModel = new TransactionModel();

        $client = $clientModel->find($id);
        if (!$client) {
            return redirect()->to('/operator/clients')->with('error', 'Client introuvable.');
        }

        $transactions = $transactionModel
            ->select('transactions.*, operation_types.name as operation_name, operation_types.code as operation_code, sender.phone_number as sender_phone, receiver.phone_number as receiver_phone')
            ->join('operation_types', 'operation_types.id = transactions.operation_type_id')
            ->join('clients as sender', 'sender.id = transactions.sender_client_id', 'left')
            ->join('clients as receiver', 'receiver.id = transactions.receiver_client_id', 'left')
            ->groupStart()
                ->where('sender_client_id', $id)
                ->orWhere('receiver_client_id', $id)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->paginate(15);

        return view('operator/clients/show', [
            'client' => $client,
            'transactions' => $transactions,
            'pager' => $transactionModel->pager
        ]);
    }
}
