<?php
// simple front controller
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/UsersController.php';
require_once __DIR__ . '/../app/core/Middleware.php';

// parse requested url from PATH_INFO
$path = $_SERVER['PATH_INFO'] ?? '/login';
$url = trim($path, '/');
if ($url === '') $url = 'login';

$routes = [
    'login' => [
        'controller' => AuthController::class,
        'action' => 'login',
        'middleware' => []
    ],
    'logout' => [
        'controller' => AuthController::class,
        'action' => 'logout',
        'middleware' => ['auth']
    ],
    'dashboard' => [
        'controller' => DashboardController::class,
        'action' => 'index',
        'middleware' => ['auth', 'role:user']
    ],
    'profile' => [
        'controller' => ProfileController::class,
        'action' => 'index',
        'middleware' => ['auth', 'role:user']
    ],
    'users' => [
        'controller' => UsersController::class,
        'action' => 'index',
        'middleware' => ['auth', 'role:admin']
    ]
];

if (!isset($routes[$url])) {
    header('HTTP/1.0 404 Not Found');
    echo "Page not found";
    exit;
}

$route = $routes[$url];
Middleware::handle($route['middleware']);

$controllerName = $route['controller'];
$action = $route['action'];
$controller = new $controllerName();
$controller->$action();
