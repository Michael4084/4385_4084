<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TransactionModel;
use CodeIgniter\Controller;

class ClientController extends Controller
{
    public function dashboard()
    {
        $clientModel = new ClientModel();
        $client = $clientModel->find(session()->get('client_id'));

        // Si le client n'existe plus en base (ex: après reset DB), rediriger vers login
        if (!$client) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }

        return view('client/dashboard', ['client' => $client]);
    }

    public function history()
    {
        $clientId = session()->get('client_id');
        $transactionModel = new TransactionModel();
        
        // Pagination & filtre
        $type = $this->request->getGet('type');

        $builder = $transactionModel
            ->select('transactions.*, operation_types.name as operation_name, operation_types.code as operation_code, sender.phone_number as sender_phone, receiver.phone_number as receiver_phone')
            ->join('operation_types', 'operation_types.id = transactions.operation_type_id')
            ->join('clients as sender', 'sender.id = transactions.sender_client_id', 'left')
            ->join('clients as receiver', 'receiver.id = transactions.receiver_client_id', 'left')
            ->groupStart()
                ->where('sender_client_id', $clientId)
                ->orWhere('receiver_client_id', $clientId)
            ->groupEnd();

        if ($type) {
            $builder->where('operation_types.code', $type);
        }

        $transactions = $builder->orderBy('created_at', 'DESC')->paginate(10);

        return view('client/history', [
            'transactions' => $transactions,
            'pager'        => $transactionModel->pager,
            'clientId'     => $clientId
        ]);
    }
}
