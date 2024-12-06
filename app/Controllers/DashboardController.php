<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    protected $session;

    public function __construct()
    {
        // Load the session service
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        return view('dashboard', ['username' => $this->session->get('username')]);
    }
}
