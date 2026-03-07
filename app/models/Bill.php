<?php

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Database.php';

class Bill extends Model
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getAll()
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return [];
        }

        $statement = $connection->query('SELECT id, table_id, created_at, updated_at FROM `bill` WHERE deleted_at IS NULL ORDER BY id DESC');
        return $statement->fetchAll();
    }

    public function findById($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return null;
        }

        $statement = $connection->prepare('SELECT id, table_id, created_at, updated_at FROM `bill` WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function createBill($tableId)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('INSERT INTO `bill` (table_id, created_at, updated_at, deleted_at) VALUES (:table_id, NOW(), NULL, NULL)');
        return $statement->execute([
            'table_id' => $tableId > 0 ? $tableId : null
        ]);
    }

    public function updateBill($id, $tableId)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `bill` SET table_id = :table_id, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $statement->execute([
            'id' => $id,
            'table_id' => $tableId > 0 ? $tableId : null
        ]);
    }

    public function deleteBill($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `bill` SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $statement->execute(['id' => $id]);
    }

    public function createBillWithItems($tableId, array $items)
    {
        $connection = $this->database->getConnection();
        if (!$connection || empty($items)) {
            return 0;
        }

        try {
            $connection->beginTransaction();

            $billStatement = $connection->prepare('INSERT INTO `bill` (table_id, created_at, updated_at, deleted_at) VALUES (:table_id, NOW(), NULL, NULL)');
            $billCreated = $billStatement->execute([
                'table_id' => $tableId > 0 ? $tableId : null
            ]);

            if (!$billCreated) {
                $connection->rollBack();
                return 0;
            }

            $billId = (int)$connection->lastInsertId();
            if ($billId <= 0) {
                $connection->rollBack();
                return 0;
            }

            $billItemStatement = $connection->prepare('INSERT INTO `billitem` (bill_id, menuitem_id, quantity, created_at, updated_at, deleted_at) VALUES (:bill_id, :menuitem_id, :quantity, NOW(), NULL, NULL)');

            foreach ($items as $item) {
                $menuitemId = (int)($item['menuitem_id'] ?? 0);
                $quantity = (int)($item['quantity'] ?? 0);

                if ($menuitemId <= 0 || $quantity <= 0) {
                    $connection->rollBack();
                    return 0;
                }

                $billItemCreated = $billItemStatement->execute([
                    'bill_id' => $billId,
                    'menuitem_id' => $menuitemId,
                    'quantity' => $quantity
                ]);

                if (!$billItemCreated) {
                    $connection->rollBack();
                    return 0;
                }
            }

            $connection->commit();
            return $billId;
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            return 0;
        }
    }
}
