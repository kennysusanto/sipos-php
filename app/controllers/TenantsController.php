<?php

require_once __DIR__ . '/../core/Controller.php';

class TenantsController extends Controller
{
    private $tenantModel;

    public function __construct()
    {
        $this->tenantModel = $this->model('Tenant');
    }

    public function index()
    {
        $tenants = $this->tenantModel->getAllTenants();

        $this->view('layouts/app', [
            'layoutTitle' => 'Tenants',
            'activePage' => 'tenants',
            'contentView' => 'tenants/index',
            'contentData' => [
                'tenants' => $tenants,
                'status' => $_GET['status'] ?? '',
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function create()
    {
        $this->view('layouts/app', [
            'layoutTitle' => 'Create Tenant',
            'activePage' => 'tenants',
            'contentView' => 'tenants/create',
            'contentData' => [
                'error' => $_GET['error'] ?? '',
                'old' => [
                    'name' => $_GET['name'] ?? '',
                    'display_name' => $_GET['display_name'] ?? ''
                ]
            ]
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tenants/create');
            exit;
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $displayName = trim((string)($_POST['display_name'] ?? ''));

        if ($name === '' || $displayName === '') {
            $this->redirectCreateWithError('Name and display name are required.', $name, $displayName);
        }

        if ($this->tenantModel->nameExists($name)) {
            $this->redirectCreateWithError('Tenant name already exists.', $name, $displayName);
        }

        $created = $this->tenantModel->createTenant($name, $displayName);
        if (!$created) {
            $this->redirectCreateWithError('Failed to create tenant.', $name, $displayName);
        }

        header('Location: /tenants?status=created');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /tenants?error=Invalid+tenant+ID.');
            exit;
        }

        $tenant = $this->tenantModel->findById($id);
        if (!$tenant) {
            header('Location: /tenants?error=Tenant+not+found.');
            exit;
        }

        $this->view('layouts/app', [
            'layoutTitle' => 'Edit Tenant',
            'activePage' => 'tenants',
            'contentView' => 'tenants/edit',
            'contentData' => [
                'tenant' => $tenant,
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tenants');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $displayName = trim((string)($_POST['display_name'] ?? ''));

        if ($id <= 0 || $name === '' || $displayName === '') {
            header('Location: /tenants?error=Invalid+tenant+data.');
            exit;
        }

        if ($this->tenantModel->nameExists($name, $id)) {
            header('Location: /tenants/edit?id=' . $id . '&error=Tenant+name+already+exists.');
            exit;
        }

        $updated = $this->tenantModel->updateTenant($id, $name, $displayName);
        if (!$updated) {
            header('Location: /tenants/edit?id=' . $id . '&error=Failed+to+update+tenant.');
            exit;
        }

        header('Location: /tenants?status=updated');
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tenants');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /tenants?error=Invalid+tenant+ID.');
            exit;
        }

        $tenant = $this->tenantModel->findById($id);
        if (!$tenant) {
            header('Location: /tenants?error=Tenant+not+found.');
            exit;
        }

        if ($this->tenantModel->hasActiveUsers($id)) {
            header('Location: /tenants?error=Cannot+delete+tenant+with+active+users.');
            exit;
        }

        $deleted = $this->tenantModel->deleteTenant($id);
        if (!$deleted) {
            header('Location: /tenants?error=Failed+to+delete+tenant.');
            exit;
        }

        header('Location: /tenants?status=deleted');
        exit;
    }

    private function redirectCreateWithError($errorMessage, $name, $displayName)
    {
        $query = http_build_query([
            'error' => $errorMessage,
            'name' => $name,
            'display_name' => $displayName
        ]);

        header('Location: /tenants/create?' . $query);
        exit;
    }
}
