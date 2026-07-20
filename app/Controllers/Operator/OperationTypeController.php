<?php

namespace App\Controllers\Operator;

use App\Models\OperationTypeModel;
use CodeIgniter\Controller;

class OperationTypeController extends Controller
{
    public function index()
    {
        $model = new OperationTypeModel();
        $types = $model->findAll();

        return view('operator/operation_types/index', ['types' => $types]);
    }

    public function store()
    {
        $model = new OperationTypeModel();
        
        $data = [
            'code' => strtoupper($this->request->getPost('code')),
            'name' => $this->request->getPost('name'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        try {
            $model->insert($data);
            return redirect()->to('/operator/operation-types')->with('success', 'Type d\'opération ajouté.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : Le code doit être unique. ' . $e->getMessage());
        }
    }

    public function toggle($id)
    {
        $model = new OperationTypeModel();
        $type = $model->find($id);
        
        if ($type) {
            $model->update($id, ['is_active' => !$type['is_active']]);
            return redirect()->to('/operator/operation-types')->with('success', 'Statut modifié.');
        }
        return redirect()->to('/operator/operation-types')->with('error', 'Type introuvable.');
    }
}
