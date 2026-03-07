<?php

require_once __DIR__ . '/../core/Controller.php';

class CashierMenuController extends Controller
{
    private $menuItemModel;
    private $billModel;

    public function __construct()
    {
        $this->menuItemModel = $this->model('MenuItem');
        $this->billModel = $this->model('Bill');
    }

    public function index()
    {
        $tenantId = $this->getTenantId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawCart = (string)($_POST['cart_payload'] ?? '');
            $cartItems = json_decode($rawCart, true);

            if (!is_array($cartItems) || empty($cartItems)) {
                header('Location: /cashiermenu?error=Please+select+at+least+one+menu+item.');
                exit;
            }

            $availableItems = $this->menuItemModel->getAll($tenantId);
            $availableById = [];
            foreach ($availableItems as $availableItem) {
                $availableById[(int)$availableItem['id']] = $availableItem;
            }

            $selectedItems = [];
            foreach ($cartItems as $cartItem) {
                $menuItemId = isset($cartItem['id']) ? (int)$cartItem['id'] : 0;
                $quantity = isset($cartItem['quantity']) ? (int)$cartItem['quantity'] : 0;

                if ($menuItemId <= 0 || $quantity <= 0) {
                    continue;
                }

                if (!isset($availableById[$menuItemId])) {
                    continue;
                }

                $selectedItems[] = [
                    'menuitem_id' => $menuItemId,
                    'quantity' => $quantity
                ];
            }

            if (empty($selectedItems)) {
                header('Location: /cashiermenu?error=Selected+items+are+invalid+for+your+tenant.');
                exit;
            }

            $billId = $this->billModel->createBillWithItems(0, $selectedItems);
            if ($billId <= 0) {
                header('Location: /cashiermenu?error=Failed+to+create+order.');
                exit;
            }

            header('Location: /bills/detail?bill_id=' . $billId . '&status=created');
            exit;
        }

        $items = $this->menuItemModel->getAll($tenantId);

        $this->view('layouts/app', [
            'layoutTitle' => 'Cashier Menu',
            'activePage' => 'cashiermenu',
            'contentView' => 'cashiermenu/index',
            'contentData' => [
                'items' => $items,
                'error' => $_GET['error'] ?? ''
            ]
        ]);
    }

    private function getTenantId()
    {
        $tenantId = (int)($_SESSION['tenant_id'] ?? 0);
        if ($tenantId <= 0) {
            header('Location: /cashiermenu?error=Tenant+context+is+missing.');
            exit;
        }

        return $tenantId;
    }
}
