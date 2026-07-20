<?php

namespace App\Controllers\Operator;

use App\Models\PhonePrefixModel;
use CodeIgniter\Controller;

class PrefixController extends Controller
{
    public function index()
    {
        $prefixModel = new PhonePrefixModel();
        $prefixes = $prefixModel->orderBy('prefix', 'ASC')->findAll();

        return view('operator/prefixes/index', ['prefixes' => $prefixes]);
    }

    public function store()
    {
        $prefixModel = new PhonePrefixModel();
        
        $data = [
            'prefix' => $this->request->getPost('prefix'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($prefixModel->save($data)) {
            return redirect()->to('/operator/prefixes')->with('success', 'Préfixe ajouté avec succès.');
        } else {
            return redirect()->back()->with('error', implode('<br>', $prefixModel->errors()));
        }
    }

    public function toggle($id)
    {
        $prefixModel = new PhonePrefixModel();
        $prefix = $prefixModel->find($id);
        
        if ($prefix) {
            $prefixModel->update($id, ['is_active' => !$prefix['is_active']]);
            return redirect()->to('/operator/prefixes')->with('success', 'Statut du préfixe modifié.');
        }
        return redirect()->to('/operator/prefixes')->with('error', 'Préfixe introuvable.');
    }

    public function delete($id)
    {
        $prefixModel = new PhonePrefixModel();
        
        // Vérification si utilisé par un client (à faire dans la vraie vie, ici simplifions)
        $db = \Config\Database::connect();
        $prefixData = $prefixModel->find($id);
        if ($prefixData) {
            $count = $db->table('clients')->like('phone_number', $prefixData['prefix'], 'after')->countAllResults();
            if ($count > 0) {
                return redirect()->to('/operator/prefixes')->with('error', 'Impossible de supprimer ce préfixe car il est utilisé par ' . $count . ' client(s).');
            }

            $prefixModel->delete($id);
            return redirect()->to('/operator/prefixes')->with('success', 'Préfixe supprimé.');
        }
        return redirect()->to('/operator/prefixes')->with('error', 'Préfixe introuvable.');
    }
}
