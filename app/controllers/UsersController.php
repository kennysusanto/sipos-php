<?php

require_once __DIR__ . '/../core/Controller.php';

class UsersController extends Controller
{
    private $userModel;
    private $tenantModel;
    private $allowedRoles = ['admin', 'user'];

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->tenantModel = $this->model('Tenant');
    }

    public function index()
    {
        $users = $this->userModel->getAllUsers();
        $this->view('layouts/app', [
            'layoutTitle' => 'Users',
            'activePage' => 'users',
            'contentView' => 'users/index',
            'contentData' => [
                'users' => $users,
                'status' => $_GET['status'] ?? '',
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function create()
    {
        $tenants = $this->tenantModel->getTenantOptions();

        $this->view('layouts/app', [
            'layoutTitle' => 'Create User',
            'activePage' => 'users',
            'contentView' => 'users/create',
            'contentData' => [
                'error' => $_GET['error'] ?? '',
                'tenants' => $tenants,
                'old' => [
                    'username' => $_GET['username'] ?? '',
                    'email' => $_GET['email'] ?? '',
                    'role' => $_GET['role'] ?? 'user',
                    'tenant_id' => $_GET['tenant_id'] ?? ''
                ]
            ]
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users/create');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $role = (string)($_POST['role'] ?? 'user');
        $tenantId = (int)($_POST['tenant_id'] ?? 0);

        if ($username === '' || $email === '' || $password === '' || $tenantId <= 0) {
            $this->redirectCreateWithError('Username, email, password, and tenant are required.', $username, $email, $role, $tenantId);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectCreateWithError('Invalid email format.', $username, $email, $role, $tenantId);
        }

        if (!in_array($role, $this->allowedRoles, true)) {
            $this->redirectCreateWithError('Invalid role selected.', $username, $email, 'user', $tenantId);
        }

        if (!$this->tenantModel->findById($tenantId)) {
            $this->redirectCreateWithError('Selected tenant not found.', $username, $email, $role, $tenantId);
        }

        if ($this->userModel->usernameExists($username)) {
            $this->redirectCreateWithError('Username already exists.', $username, $email, $role, $tenantId);
        }

        if ($this->userModel->emailExists($email)) {
            $this->redirectCreateWithError('Email already exists.', $username, $email, $role, $tenantId);
        }

        $created = $this->userModel->createUser($tenantId, $username, $email, $password, $role);
        if (!$created) {
            $this->redirectCreateWithError('Failed to create user.', $username, $email, $role, $tenantId);
        }

        header('Location: /users?status=created');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /users?error=Invalid+user+ID.');
            exit;
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            header('Location: /users?error=User+not+found.');
            exit;
        }

        $tenants = $this->tenantModel->getTenantOptions();

        $this->view('layouts/app', [
            'layoutTitle' => 'Edit User',
            'activePage' => 'users',
            'contentView' => 'users/edit',
            'contentData' => [
                'user' => $user,
                'tenants' => $tenants,
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $role = (string)($_POST['role'] ?? 'user');
        $tenantId = (int)($_POST['tenant_id'] ?? 0);

        if ($id <= 0 || $username === '' || $email === '' || $tenantId <= 0) {
            header('Location: /users?error=Invalid+user+data.');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /users/edit?id=' . $id . '&error=Invalid+email+format.');
            exit;
        }

        if (!in_array($role, $this->allowedRoles, true)) {
            header('Location: /users/edit?id=' . $id . '&error=Invalid+role+selected.');
            exit;
        }

        if (!$this->tenantModel->findById($tenantId)) {
            header('Location: /users/edit?id=' . $id . '&error=Selected+tenant+not+found.');
            exit;
        }

        if ($this->userModel->usernameExists($username, $id)) {
            header('Location: /users/edit?id=' . $id . '&error=Username+already+exists.');
            exit;
        }

        if ($this->userModel->emailExists($email, $id)) {
            header('Location: /users/edit?id=' . $id . '&error=Email+already+exists.');
            exit;
        }

        $updated = $this->userModel->updateUser($id, $tenantId, $username, $email, $role, $password);
        if (!$updated) {
            header('Location: /users/edit?id=' . $id . '&error=Failed+to+update+user.');
            exit;
        }

        header('Location: /users?status=updated');
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /users?error=Invalid+user+ID.');
            exit;
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            header('Location: /users?error=User+not+found.');
            exit;
        }

        $currentUsername = $_SESSION['user'] ?? '';
        if (($user['username'] ?? '') === $currentUsername) {
            header('Location: /users?error=You+cannot+delete+your+own+account.');
            exit;
        }

        $deleted = $this->userModel->deleteUser($id);
        if (!$deleted) {
            header('Location: /users?error=Failed+to+delete+user.');
            exit;
        }

        header('Location: /users?status=deleted');
        exit;
    }

    private function redirectCreateWithError($errorMessage, $username, $email, $role, $tenantId)
    {
        $query = http_build_query([
            'error' => $errorMessage,
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'tenant_id' => $tenantId
        ]);

        header('Location: /users/create?' . $query);
        exit;
    }
}
