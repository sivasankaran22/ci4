<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $session;

    public function __construct()
    {
        // Load the session service
        $this->session = \Config\Services::session();
    }

    public function register()
    {
        helper(['form', 'url']);

        if ($this->request->getMethod() == 'POST') {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => 'required|min_length[3]|max_length[100]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return view('register', ['validation' => $validation]);
            }

            $userModel = new UserModel();
            $userModel->save([
                'username' => $this->request->getPost('username'),
                'email'    => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            ]);

            return redirect()->to('/login')->with('success', 'Registration successful! You can now log in.');
        }

        return view('register');
    }

    public function login()
    {
        helper(['form', 'url']);

        if ($this->request->getMethod() == 'POST') {
            $userModel = new UserModel();
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $user = $userModel->where('email', $email)->first();

            if ($user && password_verify($password, $user['password'])) {
                $this->session->set([
                    'user_id'   => $user['id'],
                    'username'  => $user['username'],
                    'isLoggedIn' => true,
                ]);
                return redirect()->to('/dashboard');
            } else {
                return view('login', ['error' => 'Invalid email or password']);
            }
        }

        return view('login');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}
