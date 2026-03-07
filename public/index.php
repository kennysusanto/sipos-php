<?php
// simple front controller
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/UsersController.php';
require_once __DIR__ . '/../app/controllers/TenantsController.php';
require_once __DIR__ . '/../app/controllers/MenuItemsController.php';
require_once __DIR__ . '/../app/controllers/BillsController.php';
require_once __DIR__ . '/../app/controllers/BillItemsController.php';
require_once __DIR__ . '/../app/controllers/CashierMenuController.php';
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
    'unauthorized' => [
        'controller' => AuthController::class,
        'action' => 'unauthorized',
        'middleware' => []
    ],
    'not-found' => [
        'controller' => AuthController::class,
        'action' => 'notFound',
        'middleware' => []
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
    ],
    'users/create' => [
        'controller' => UsersController::class,
        'action' => 'create',
        'middleware' => ['auth', 'role:admin']
    ],
    'users/store' => [
        'controller' => UsersController::class,
        'action' => 'store',
        'middleware' => ['auth', 'role:admin']
    ],
    'users/edit' => [
        'controller' => UsersController::class,
        'action' => 'edit',
        'middleware' => ['auth', 'role:admin']
    ],
    'users/update' => [
        'controller' => UsersController::class,
        'action' => 'update',
        'middleware' => ['auth', 'role:admin']
    ],
    'users/delete' => [
        'controller' => UsersController::class,
        'action' => 'delete',
        'middleware' => ['auth', 'role:admin']
    ],
    'tenants' => [
        'controller' => TenantsController::class,
        'action' => 'index',
        'middleware' => ['auth', 'role:admin']
    ],
    'tenants/create' => [
        'controller' => TenantsController::class,
        'action' => 'create',
        'middleware' => ['auth', 'role:admin']
    ],
    'tenants/store' => [
        'controller' => TenantsController::class,
        'action' => 'store',
        'middleware' => ['auth', 'role:admin']
    ],
    'tenants/edit' => [
        'controller' => TenantsController::class,
        'action' => 'edit',
        'middleware' => ['auth', 'role:admin']
    ],
    'tenants/update' => [
        'controller' => TenantsController::class,
        'action' => 'update',
        'middleware' => ['auth', 'role:admin']
    ],
    'tenants/delete' => [
        'controller' => TenantsController::class,
        'action' => 'delete',
        'middleware' => ['auth', 'role:admin']
    ],
    'menuitems' => [
        'controller' => MenuItemsController::class,
        'action' => 'index',
        'middleware' => ['auth', 'role:admin']
    ],
    'menuitems/create' => [
        'controller' => MenuItemsController::class,
        'action' => 'create',
        'middleware' => ['auth', 'role:admin']
    ],
    'menuitems/store' => [
        'controller' => MenuItemsController::class,
        'action' => 'store',
        'middleware' => ['auth', 'role:admin']
    ],
    'menuitems/edit' => [
        'controller' => MenuItemsController::class,
        'action' => 'edit',
        'middleware' => ['auth', 'role:admin']
    ],
    'menuitems/update' => [
        'controller' => MenuItemsController::class,
        'action' => 'update',
        'middleware' => ['auth', 'role:admin']
    ],
    'menuitems/delete' => [
        'controller' => MenuItemsController::class,
        'action' => 'delete',
        'middleware' => ['auth', 'role:admin']
    ],
    'bills' => [
        'controller' => BillsController::class,
        'action' => 'index',
        'middleware' => ['auth', 'role:user']
    ],
    'cashiermenu' => [
        'controller' => CashierMenuController::class,
        'action' => 'index',
        'middleware' => ['auth', 'role:user']
    ],
    'bills/create' => [
        'controller' => BillsController::class,
        'action' => 'create',
        'middleware' => ['auth', 'role:user']
    ],
    'bills/store' => [
        'controller' => BillsController::class,
        'action' => 'store',
        'middleware' => ['auth', 'role:user']
    ],
    'bills/edit' => [
        'controller' => BillsController::class,
        'action' => 'edit',
        'middleware' => ['auth', 'role:user']
    ],
    'bills/update' => [
        'controller' => BillsController::class,
        'action' => 'update',
        'middleware' => ['auth', 'role:user']
    ],
    'bills/delete' => [
        'controller' => BillsController::class,
        'action' => 'delete',
        'middleware' => ['auth', 'role:user']
    ],
    'bills/detail' => [
        'controller' => BillItemsController::class,
        'action' => 'index',
        'middleware' => ['auth', 'role:user']
    ],
    'billitems/create' => [
        'controller' => BillItemsController::class,
        'action' => 'create',
        'middleware' => ['auth', 'role:user']
    ],
    'billitems/store' => [
        'controller' => BillItemsController::class,
        'action' => 'store',
        'middleware' => ['auth', 'role:user']
    ],
    'billitems/edit' => [
        'controller' => BillItemsController::class,
        'action' => 'edit',
        'middleware' => ['auth', 'role:user']
    ],
    'billitems/update' => [
        'controller' => BillItemsController::class,
        'action' => 'update',
        'middleware' => ['auth', 'role:user']
    ],
    'billitems/delete' => [
        'controller' => BillItemsController::class,
        'action' => 'delete',
        'middleware' => ['auth', 'role:user']
    ]
];

if (!isset($routes[$url])) {
    header('Location: /not-found');
    exit;
}

$route = $routes[$url];
Middleware::handle($route['middleware']);

$controllerName = $route['controller'];
$action = $route['action'];
$controller = new $controllerName();
$controller->$action();
