<?php

require_once __DIR__ . '/../core/Controller.php';

class MenuItemsController extends Controller
{
    private $menuItemModel;

    public function __construct()
    {
        $this->menuItemModel = $this->model('MenuItem');
    }

    public function index()
    {
        $tenantId = $this->getTenantId();
        $items = $this->menuItemModel->getAll($tenantId);

        $this->view('layouts/app', [
            'layoutTitle' => 'Menu Items',
            'activePage' => 'menuitems',
            'contentView' => 'menuitems/index',
            'contentData' => [
                'items' => $items,
                'status' => $_GET['status'] ?? '',
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function create()
    {
        $tenantId = $this->getTenantId();

        $this->view('layouts/app', [
            'layoutTitle' => 'Create Menu Item',
            'activePage' => 'menuitems',
            'contentView' => 'menuitems/create',
            'contentData' => [
                'error' => $_GET['error'] ?? '',
                'old' => [
                    'tenant_id' => $_GET['tenant_id'] ?? (string)$tenantId,
                    'display_name' => $_GET['display_name'] ?? '',
                    'name' => $_GET['name'] ?? '',
                    'url' => $_GET['url'] ?? '',
                    'description' => $_GET['description'] ?? '',
                    'price' => $_GET['price'] ?? '',
                    'stock' => $_GET['stock'] ?? ''
                ]
            ]
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /menuitems/create');
            exit;
        }

        $tenantId = $this->getTenantId();

        $payload = $this->sanitizeFormInput();

        if ((int)$payload['tenant_id'] !== $tenantId) {
            $this->redirectCreateWithError('Invalid tenant context.', $payload);
        }

        $validationError = $this->validatePayload($payload);
        if ($validationError !== null) {
            $this->redirectCreateWithError($validationError, $payload);
        }

        if ($this->menuItemModel->nameExists($payload['name'], $tenantId)) {
            $this->redirectCreateWithError('Internal name already exists.', $payload);
        }

        $created = $this->menuItemModel->create(
            (int)$payload['tenant_id'],
            $payload['display_name'],
            $payload['name'],
            $payload['url'],
            $payload['description'],
            $payload['price'],
            $payload['stock']
        );

        if (!$created) {
            $this->redirectCreateWithError('Failed to create menu item.', $payload);
        }

        header('Location: /menuitems?status=created');
        exit;
    }

    public function edit()
    {
        $tenantId = $this->getTenantId();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /menuitems?error=Invalid+menu+item+ID.');
            exit;
        }

        $item = $this->menuItemModel->findById($id, $tenantId);
        if (!$item) {
            header('Location: /menuitems?error=Menu+item+not+found.');
            exit;
        }

        $this->view('layouts/app', [
            'layoutTitle' => 'Edit Menu Item',
            'activePage' => 'menuitems',
            'contentView' => 'menuitems/edit',
            'contentData' => [
                'item' => $item,
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /menuitems');
            exit;
        }

        $tenantId = $this->getTenantId();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /menuitems?error=Invalid+menu+item+ID.');
            exit;
        }

        $payload = $this->sanitizeFormInput();

        if ((int)$payload['tenant_id'] !== $tenantId) {
            header('Location: /menuitems/edit?id=' . $id . '&error=Invalid+tenant+context.');
            exit;
        }

        $validationError = $this->validatePayload($payload);
        if ($validationError !== null) {
            header('Location: /menuitems/edit?id=' . $id . '&error=' . urlencode($validationError));
            exit;
        }

        if ($this->menuItemModel->nameExists($payload['name'], $tenantId, $id)) {
            header('Location: /menuitems/edit?id=' . $id . '&error=Internal+name+already+exists.');
            exit;
        }

        $updated = $this->menuItemModel->update(
            $id,
            (int)$payload['tenant_id'],
            $payload['display_name'],
            $payload['name'],
            $payload['url'],
            $payload['description'],
            $payload['price'],
            $payload['stock']
        );

        if (!$updated) {
            header('Location: /menuitems/edit?id=' . $id . '&error=Failed+to+update+menu+item.');
            exit;
        }

        header('Location: /menuitems?status=updated');
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /menuitems');
            exit;
        }

        $tenantId = $this->getTenantId();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /menuitems?error=Invalid+menu+item+ID.');
            exit;
        }

        $deleted = $this->menuItemModel->softDelete($id, $tenantId);
        if (!$deleted) {
            header('Location: /menuitems?error=Failed+to+delete+menu+item.');
            exit;
        }

        header('Location: /menuitems?status=deleted');
        exit;
    }

    private function sanitizeFormInput()
    {
        return [
            'tenant_id' => (string)($_POST['tenant_id'] ?? ''),
            'display_name' => trim((string)($_POST['display_name'] ?? '')),
            'name' => trim((string)($_POST['name'] ?? '')),
            'url' => trim((string)($_POST['url'] ?? '')),
            'description' => trim((string)($_POST['description'] ?? '')),
            'price' => (string)($_POST['price'] ?? ''),
            'stock' => (string)($_POST['stock'] ?? '')
        ];
    }

    private function validatePayload(array $payload)
    {
        if ($payload['display_name'] === '' || $payload['name'] === '' || $payload['description'] === '') {
            return 'Display name, name, and description are required.';
        }

        if ($payload['tenant_id'] === '' || !ctype_digit($payload['tenant_id']) || (int)$payload['tenant_id'] <= 0) {
            return 'Tenant is required.';
        }

        if ($payload['url'] !== '' && !filter_var($payload['url'], FILTER_VALIDATE_URL)) {
            return 'URL must be a valid link.';
        }

        if (!is_numeric($payload['price']) || (float)$payload['price'] < 0) {
            return 'Price must be a valid non-negative number.';
        }

        if (!ctype_digit($payload['stock'])) {
            return 'Stock must be a non-negative integer.';
        }

        return null;
    }

    private function redirectCreateWithError($errorMessage, array $payload)
    {
        $query = http_build_query([
            'error' => $errorMessage,
            'tenant_id' => $payload['tenant_id'],
            'display_name' => $payload['display_name'],
            'name' => $payload['name'],
            'url' => $payload['url'],
            'description' => $payload['description'],
            'price' => $payload['price'],
            'stock' => $payload['stock']
        ]);

        header('Location: /menuitems/create?' . $query);
        exit;
    }

    private function getTenantId()
    {
        $tenantId = (int)($_SESSION['tenant_id'] ?? 0);
        if ($tenantId <= 0) {
            header('Location: /menuitems?error=Tenant+context+is+missing.');
            exit;
        }

        return $tenantId;
    }
}
