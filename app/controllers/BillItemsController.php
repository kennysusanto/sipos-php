<?php

require_once __DIR__ . '/../core/Controller.php';

class BillItemsController extends Controller
{
    private $billItemModel;
    private $billModel;
    private $menuItemModel;

    public function __construct()
    {
        $this->billItemModel = $this->model('BillItem');
        $this->billModel = $this->model('Bill');
        $this->menuItemModel = $this->model('MenuItem');
    }

    public function index()
    {
        $billId = (int)($_GET['bill_id'] ?? 0);
        if ($billId <= 0) {
            header('Location: /bills?error=Bill+ID+is+required.');
            exit;
        }

        $bill = $this->billModel->findById($billId);
        if (!$bill) {
            header('Location: /bills?error=Bill+not+found.');
            exit;
        }

        $items = $this->billItemModel->getByBillId($billId);

        $this->view('layouts/app', [
            'layoutTitle' => 'Bill Detail',
            'activePage' => 'bills',
            'contentView' => 'billitems/index',
            'contentData' => [
                'bill' => $bill,
                'items' => $items,
                'status' => $_GET['status'] ?? '',
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function create()
    {
        $billId = (int)($_GET['bill_id'] ?? 0);
        if ($billId <= 0) {
            header('Location: /bills?error=Bill+ID+is+required.');
            exit;
        }

        $bill = $this->billModel->findById($billId);
        if (!$bill) {
            header('Location: /bills?error=Bill+not+found.');
            exit;
        }

        $tenantId = (int)($_SESSION['tenant_id'] ?? 0);
        $menuItems = $this->menuItemModel->getAll($tenantId);

        $this->view('layouts/app', [
            'layoutTitle' => 'Add Bill Item',
            'activePage' => 'bills',
            'contentView' => 'billitems/create',
            'contentData' => [
                'bill' => $bill,
                'menuItems' => $menuItems,
                'error' => $_GET['error'] ?? '',
                'old' => [
                    'menuitem_id' => $_GET['menuitem_id'] ?? '',
                    'quantity' => $_GET['quantity'] ?? '1'
                ]
            ]
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /bills');
            exit;
        }

        $billId = (int)($_POST['bill_id'] ?? 0);
        $menuitemId = (int)($_POST['menuitem_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($billId <= 0) {
            header('Location: /bills?error=Bill+ID+is+required.');
            exit;
        }

        $bill = $this->billModel->findById($billId);
        if (!$bill) {
            header('Location: /bills?error=Bill+not+found.');
            exit;
        }

        if ($menuitemId <= 0 || $quantity <= 0) {
            $query = http_build_query([
                'bill_id' => $billId,
                'error' => 'Menu+item+and+quantity+are+required.',
                'menuitem_id' => $menuitemId,
                'quantity' => $quantity
            ]);
            header('Location: /billitems/create?' . $query);
            exit;
        }

        $tenantId = (int)($_SESSION['tenant_id'] ?? 0);
        $selectedMenuItem = $this->menuItemModel->findById($menuitemId, $tenantId);
        if (!$selectedMenuItem) {
            $query = http_build_query([
                'bill_id' => $billId,
                'error' => 'Selected+menu+item+is+invalid+for+your+tenant.',
                'menuitem_id' => $menuitemId,
                'quantity' => $quantity
            ]);
            header('Location: /billitems/create?' . $query);
            exit;
        }

        $created = $this->billItemModel->createBillItem($billId, $menuitemId, $quantity);
        if (!$created) {
            header('Location: /bills/detail?bill_id=' . $billId . '&error=Failed+to+add+bill+item.');
            exit;
        }

        header('Location: /bills/detail?bill_id=' . $billId . '&status=created');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /bills?error=Invalid+bill+item+ID.');
            exit;
        }

        $billItem = $this->billItemModel->findById($id);
        if (!$billItem) {
            header('Location: /bills?error=Bill+item+not+found.');
            exit;
        }

        $bill = $this->billModel->findById((int)$billItem['bill_id']);
        if (!$bill) {
            header('Location: /bills?error=Parent+bill+not+found.');
            exit;
        }

        $tenantId = (int)($_SESSION['tenant_id'] ?? 0);
        $menuItems = $this->menuItemModel->getAll($tenantId);

        $this->view('layouts/app', [
            'layoutTitle' => 'Edit Bill Item',
            'activePage' => 'bills',
            'contentView' => 'billitems/edit',
            'contentData' => [
                'bill' => $bill,
                'billItem' => $billItem,
                'menuItems' => $menuItems,
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /bills');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $billId = (int)($_POST['bill_id'] ?? 0);
        $menuitemId = (int)($_POST['menuitem_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($id <= 0 || $billId <= 0) {
            header('Location: /bills?error=Invalid+bill+item+data.');
            exit;
        }

        if ($menuitemId <= 0 || $quantity <= 0) {
            header('Location: /billitems/edit?id=' . $id . '&error=Menu+item+and+quantity+are+required.');
            exit;
        }

        $tenantId = (int)($_SESSION['tenant_id'] ?? 0);
        $selectedMenuItem = $this->menuItemModel->findById($menuitemId, $tenantId);
        if (!$selectedMenuItem) {
            header('Location: /billitems/edit?id=' . $id . '&error=Selected+menu+item+is+invalid+for+your+tenant.');
            exit;
        }

        $updated = $this->billItemModel->updateBillItem($id, $menuitemId, $quantity);
        if (!$updated) {
            header('Location: /billitems/edit?id=' . $id . '&error=Failed+to+update+bill+item.');
            exit;
        }

        header('Location: /bills/detail?bill_id=' . $billId . '&status=updated');
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /bills');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $billId = (int)($_POST['bill_id'] ?? 0);

        if ($id <= 0 || $billId <= 0) {
            header('Location: /bills?error=Invalid+bill+item+ID.');
            exit;
        }

        $billItem = $this->billItemModel->findById($id);
        if (!$billItem || (int)$billItem['bill_id'] !== $billId) {
            header('Location: /bills/detail?bill_id=' . $billId . '&error=Bill+item+not+found.');
            exit;
        }

        $deleted = $this->billItemModel->deleteBillItem($id);
        if (!$deleted) {
            header('Location: /bills/detail?bill_id=' . $billId . '&error=Failed+to+delete+bill+item.');
            exit;
        }

        header('Location: /bills/detail?bill_id=' . $billId . '&status=deleted');
        exit;
    }
}
