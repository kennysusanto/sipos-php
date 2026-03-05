<?php

require_once __DIR__ . '/../core/Controller.php';

class DashboardController extends Controller
{
    public function index()
    {
        $this->view('layouts/app', [
            'layoutTitle' => 'Dashboard',
            'activePage' => 'dashboard',
            'contentView' => 'dashboard/index',
            'contentData' => [
                'username' => $_SESSION['user'] ?? ''
            ]
        ]);
    }
}
