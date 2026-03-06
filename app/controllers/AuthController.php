<?php

require_once __DIR__ . '/../core/Controller.php';

class AuthController extends Controller
{
    private $config;
    private $userModel;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/config.php';
        $this->userModel = $this->model('User');        
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $authenticatedUser = $this->userModel->authenticate($username, $password);
            if ($authenticatedUser) {
                $_SESSION['user'] = $authenticatedUser['username'] ?? $username;
                $_SESSION['role'] = $authenticatedUser['role'] ?? 'user';
                $_SESSION['tenant_id'] = $authenticatedUser['tenant_id'] ?? null;
                $_SESSION['tenant_name'] = $authenticatedUser['tenant_name'] ?? null;
                header('Location: /dashboard');
                exit;
            } else {
                $error = 'Invalid credentials';
                $this->view('auth/login', ['error' => $error]);
                return;
            }
        }
        $this->view('auth/login');
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
