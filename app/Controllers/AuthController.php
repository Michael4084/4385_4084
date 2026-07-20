<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PhonePrefixModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function login()
    {
        if (session()->get('client_id')) {
            return redirect()->to('/client/dashboard');
        }
        return view('auth/client_login');
    }

    public function processLogin()
    {
        $phoneNumber = $this->request->getPost('phone_number');

        if (!$phoneNumber || !preg_match('/^[0-9]{10}$/', $phoneNumber)) {
            return redirect()->back()->with('error', 'Le numéro de téléphone doit contenir exactement 10 chiffres.');
        }

        $prefixModel = new PhonePrefixModel();
        $clientModel = new ClientModel();

        // Extraire le préfixe (ex: 034 de 0340000001)
        $prefix = substr($phoneNumber, 0, 3);
        
        // Vérifier si le préfixe est valide et actif
        $validPrefix = $prefixModel->where('prefix', $prefix)->where('is_active', 1)->first();
        
        if (!$validPrefix) {
            return redirect()->back()->with('error', 'Préfixe invalide ou non pris en charge par l\'opérateur.');
        }

        // Vérifier si le client existe
        $client = $clientModel->where('phone_number', $phoneNumber)->first();

        // S'il n'existe pas, on le crée avec un solde de 0
        if (!$client) {
            $clientId = $clientModel->insert([
                'phone_number' => $phoneNumber,
                'balance'      => 0.00,
                'status'       => 'active'
            ]);
            $client = $clientModel->find($clientId);
        } else if ($client['status'] !== 'active') {
            return redirect()->back()->with('error', 'Votre compte est suspendu.');
        }

        // Créer la session
        session()->set([
            'client_id'    => $client['id'],
            'phone_number' => $client['phone_number'],
            'logged_in'    => true
        ]);

        return redirect()->to('/client/dashboard')->with('success', 'Connexion réussie.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
