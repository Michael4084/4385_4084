<?php

namespace App\Controllers\Operator;

use App\Models\FeeBracketModel;
use App\Models\OperationTypeModel;
use CodeIgniter\Controller;

class FeeBracketController extends Controller
{
    public function index()
    {
        $feeModel = new FeeBracketModel();
        $opTypeModel = new OperationTypeModel();

        $brackets = $feeModel->select('fee_brackets.*, operation_types.name as operation_name')
                             ->join('operation_types', 'operation_types.id = fee_brackets.operation_type_id')
                             ->orderBy('operation_type_id', 'ASC')
                             ->orderBy('min_amount', 'ASC')
                             ->findAll();

        $operationTypes = $opTypeModel->findAll();

        return view('operator/fee_brackets/index', [
            'brackets' => $brackets,
            'operationTypes' => $operationTypes
        ]);
    }

    public function store()
    {
        $feeModel = new FeeBracketModel();
        
        $data = [
            'operation_type_id' => $this->request->getPost('operation_type_id'),
            'min_amount'        => $this->request->getPost('min_amount'),
            'max_amount'        => $this->request->getPost('max_amount'),
            'fee_amount'        => $this->request->getPost('fee_amount')
        ];

        // Validation simple contre les chevauchements
        $overlap = $feeModel->where('operation_type_id', $data['operation_type_id'])
                            ->where('min_amount <=', $data['max_amount'])
                            ->where('max_amount >=', $data['min_amount'])
                            ->first();

        if ($overlap) {
            return redirect()->back()->withInput()->with('error', 'Ces tranches se chevauchent avec une tranche existante.');
        }

        if ($data['min_amount'] > $data['max_amount']) {
            return redirect()->back()->withInput()->with('error', 'Le montant minimum ne peut pas être supérieur au montant maximum.');
        }

        if ($feeModel->save($data)) {
            return redirect()->to('/operator/fees')->with('success', 'Barème ajouté.');
        } else {
            return redirect()->back()->withInput()->with('error', implode('<br>', $feeModel->errors()));
        }
    }

    public function delete($id)
    {
        $feeModel = new FeeBracketModel();
        $feeModel->delete($id);
        return redirect()->to('/operator/fees')->with('success', 'Barème supprimé.');
    }
}
