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
}
