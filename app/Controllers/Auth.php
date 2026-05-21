<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return view('V_login');
    }

    public function attempt()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez renseigner vos identifiants.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Identifiants incorrects.');
        }

        $accountStatus = $user['account_status'] ?? 'approved';

        if ($accountStatus === 'pending') {
            return redirect()->back()->withInput()->with('error', 'Votre compte est en attente de confirmation par l\'administrateur.');
        }

        if ($accountStatus === 'rejected') {
            return redirect()->back()->withInput()->with('error', 'Votre compte a ete refuse par l\'administrateur.');
        }

        session()->set([
            'user_id' => $user['id'],
            'shop_id' => $user['shop_id'],
            'is_admin' => $user['is_admin'],
            'email' => $user['email'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/')->with('success', 'Connexion reussie.');
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return view('V_register');
    }

    public function store()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = $this->request->getPost('password');

        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $userModel = new UserModel();

        $shopId = bin2hex(random_bytes(12));
        while ($userModel->where('shop_id', $shopId)->first()) {
            $shopId = bin2hex(random_bytes(12));
        }

        $userModel->save([
            'shop_id' => $shopId,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'account_status' => 'pending',
        ]);

        return redirect()->to('/login')->with('success', 'Compte cree. Attendez la confirmation de l\'administrateur avant de vous connecter.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}
