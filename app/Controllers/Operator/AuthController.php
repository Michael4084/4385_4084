<?php

namespace App\Controllers\Operator;

use App\Models\OperatorModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function login()
    {
        if (session()->get('operator_id')) {
            return redirect()->to('/operator/dashboard');
        }
        return view('auth/operator_login');
    }

    public function processLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (!$username || !$password) {
            return redirect()->back()->with('error', 'Veuillez remplir tous les champs.');
        }

        $operatorModel = new OperatorModel();
        $operator = $operatorModel->where('username', $username)->first();

        if ($operator && password_verify($password, $operator['password_hash'])) {
            session()->set([
                'operator_id' => $operator['id'],
                'operator_username' => $operator['username'],
                'operator_logged_in' => true
            ]);
            return redirect()->to('/operator/dashboard')->with('success', 'Connexion réussie.');
        } else {
            return redirect()->back()->with('error', 'Identifiants incorrects.');
        }
    }

    public function logout()
    {
        session()->remove(['operator_id', 'operator_username', 'operator_logged_in']);
        return redirect()->to('/operator/login');
    }
}
