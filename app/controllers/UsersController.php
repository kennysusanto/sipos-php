<?php

require_once __DIR__ . '/../core/Controller.php';

class UsersController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        $users = $this->userModel->getAllUsers();
        $this->view('layouts/app', [
            'layoutTitle' => 'Users',
            'activePage' => 'users',
            'contentView' => 'users/index',
            'contentData' => [
                'users' => $users
            ]
        ]);
    }
}
