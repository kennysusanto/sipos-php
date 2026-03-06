<?php

require_once __DIR__ . '/../core/Controller.php';

class BillsController extends Controller
{
    private $billModel;

    public function __construct()
    {
        $this->billModel = $this->model('Bill');
    }

    public function index()
    {
        $bills = $this->billModel->getAll();

        $this->view('layouts/app', [
            'layoutTitle' => 'Bills',
            'activePage' => 'bills',
            'contentView' => 'bills/index',
            'contentData' => [
                'bills' => $bills,
                'status' => $_GET['status'] ?? '',
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    public function create()
    {
        $this->view('layouts/app', [
            'layoutTitle' => 'Create Bill',
            'activePage' => 'bills',
            'contentView' => 'bills/create',
            'contentData' => [
                'error' => $_GET['error'] ?? '',
                'old' => [
                    'table_id' => $_GET['table_id'] ?? ''
                ]
            ]
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /bills/create');
            exit;
        }

        $tableIdRaw = trim((string)($_POST['table_id'] ?? ''));
        $tableId = $tableIdRaw === '' ? 0 : (int)$tableIdRaw;

        if ($tableIdRaw !== '' && (!ctype_digit($tableIdRaw) || $tableId <= 0)) {
            header('Location: /bills/create?error=Table+ID+must+be+a+positive+integer.&table_id=' . urlencode($tableIdRaw));
            exit;
        }

        $created = $this->billModel->createBill($tableId);
        if (!$created) {
            header('Location: /bills/create?error=Failed+to+create+bill.&table_id=' . urlencode($tableIdRaw));
            exit;
        }

        header('Location: /bills?status=created');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /bills?error=Invalid+bill+ID.');
            exit;
        }

        $bill = $this->billModel->findById($id);
        if (!$bill) {
            header('Location: /bills?error=Bill+not+found.');
            exit;
        }

        $this->view('layouts/app', [
            'layoutTitle' => 'Edit Bill',
            'activePage' => 'bills',
            'contentView' => 'bills/edit',
            'contentData' => [
                'bill' => $bill,
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
        $tableIdRaw = trim((string)($_POST['table_id'] ?? ''));
        $tableId = $tableIdRaw === '' ? 0 : (int)$tableIdRaw;

        if ($id <= 0) {
            header('Location: /bills?error=Invalid+bill+ID.');
            exit;
        }

        if ($tableIdRaw !== '' && (!ctype_digit($tableIdRaw) || $tableId <= 0)) {
            header('Location: /bills/edit?id=' . $id . '&error=Table+ID+must+be+a+positive+integer.');
            exit;
        }

        $updated = $this->billModel->updateBill($id, $tableId);
        if (!$updated) {
            header('Location: /bills/edit?id=' . $id . '&error=Failed+to+update+bill.');
            exit;
        }

        header('Location: /bills?status=updated');
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /bills');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /bills?error=Invalid+bill+ID.');
            exit;
        }

        $bill = $this->billModel->findById($id);
        if (!$bill) {
            header('Location: /bills?error=Bill+not+found.');
            exit;
        }

        $deleted = $this->billModel->deleteBill($id);
        if (!$deleted) {
            header('Location: /bills?error=Failed+to+delete+bill.');
            exit;
        }

        header('Location: /bills?status=deleted');
        exit;
    }
}
