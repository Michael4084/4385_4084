<?php

namespace App\Controllers\Operator;

use App\Models\OperatorCommissionModel;
use App\Models\OperationTypeModel;
use App\Models\OperatorModel;
use CodeIgniter\Controller;

class CommissionController extends Controller
{
    public function index()
    {
        $operatorModel = new OperatorModel();
        $operationTypeModel = new OperationTypeModel();
        $commissionModel = new OperatorCommissionModel();

        $operators = $operatorModel->findAll();
        $operationTypes = $operationTypeModel->findAll();
        $commissions = $commissionModel
            ->select('operator_commissions.*, operators.username as operator_name, operation_types.name as operation_name')
            ->join('operators', 'operators.id = operator_commissions.operator_id')
            ->join('operation_types', 'operation_types.id = operator_commissions.operation_type_id')
            ->orderBy('operator_id', 'ASC')
            ->findAll();

        return view('operator/commissions/index', [
            'operators' => $operators,
            'operationTypes' => $operationTypes,
            'commissions' => $commissions,
        ]);
    }

    public function store()
    {
        $commissionModel = new OperatorCommissionModel();

        $data = [
            'operator_id' => $this->request->getPost('operator_id'),
            'operation_type_id' => $this->request->getPost('operation_type_id'),
            'commission_percentage' => $this->request->getPost('commission_percentage'),
            'min_amount' => $this->request->getPost('min_amount') ?: null,
            'max_amount' => $this->request->getPost('max_amount') ?: null,
        ];

        if ($commissionModel->save($data)) {
            return redirect()->to('/operator/commissions')->with('success', 'Commission enregistrée.');
        }

        return redirect()->back()->withInput()->with('error', implode('<br>', $commissionModel->errors()));
    }
}
