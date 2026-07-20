<?php

namespace App\Controllers\Operator;

use App\Models\TransactionModel;
use App\Models\OperationTypeModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $transactionModel = new TransactionModel();
        $operationTypeModel = new OperationTypeModel();
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

        $operationTypes = $operationTypeModel->findAll();

        return view('operator/dashboard', [
            'stats' => $stats,
            'operationTypes' => $operationTypes,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type_code' => $typeCode
            ]
        ]);
    }
}
