<?php

require_once __DIR__ . '/../core/Controller.php';

class ProfileController extends Controller
{
    public function index()
    {
        $this->view('layouts/app', [
            'layoutTitle' => 'Profile',
            'activePage' => 'profile',
            'contentView' => 'profile/index',
            'contentData' => [
                'username' => $_SESSION['user'] ?? ''
            ]
        ]);
    }
}
