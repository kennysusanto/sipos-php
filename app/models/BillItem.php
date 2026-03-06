<?php

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Database.php';

class BillItem extends Model
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getByBillId($billId)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return [];
        }

        $statement = $connection->prepare('SELECT bi.id, bi.bill_id, bi.menuitem_id, m.display_name AS menuitem_display_name, bi.quantity, bi.created_at, bi.updated_at FROM `billitem` bi INNER JOIN `menuitem` m ON m.id = bi.menuitem_id WHERE bi.bill_id = :bill_id AND bi.deleted_at IS NULL ORDER BY bi.id DESC');
        $statement->execute(['bill_id' => $billId]);
        return $statement->fetchAll();
    }

    public function findById($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return null;
        }

        $statement = $connection->prepare('SELECT id, bill_id, menuitem_id, quantity, created_at, updated_at FROM `billitem` WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function createBillItem($billId, $menuitemId, $quantity)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('INSERT INTO `billitem` (bill_id, menuitem_id, quantity, created_at, updated_at, deleted_at) VALUES (:bill_id, :menuitem_id, :quantity, NOW(), NULL, NULL)');
        return $statement->execute([
            'bill_id' => $billId,
            'menuitem_id' => $menuitemId,
            'quantity' => $quantity
        ]);
    }

    public function updateBillItem($id, $menuitemId, $quantity)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `billitem` SET menuitem_id = :menuitem_id, quantity = :quantity, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $statement->execute([
            'id' => $id,
            'menuitem_id' => $menuitemId,
            'quantity' => $quantity
        ]);
    }

    public function deleteBillItem($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `billitem` SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $statement->execute(['id' => $id]);
    }
}
