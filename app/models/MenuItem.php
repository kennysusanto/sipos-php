<?php

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Database.php';

class MenuItem extends Model
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getAll($tenantId)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return [];
        }

        $statement = $connection->prepare('SELECT id, tenant_id, display_name, name, url, description, price, stock, created_at, updated_at FROM `menuitem` WHERE tenant_id = :tenant_id AND deleted_at IS NULL ORDER BY id DESC');
        $statement->execute(['tenant_id' => $tenantId]);
        return $statement->fetchAll();
    }

    public function findById($id, $tenantId)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return null;
        }

        $statement = $connection->prepare('SELECT id, tenant_id, display_name, name, url, description, price, stock, created_at, updated_at FROM `menuitem` WHERE id = :id AND tenant_id = :tenant_id AND deleted_at IS NULL LIMIT 1');
        $statement->execute([
            'id' => $id,
            'tenant_id' => $tenantId
        ]);
        return $statement->fetch() ?: null;
    }

    public function nameExists($name, $tenantId, $excludeId = null)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        if ($excludeId !== null) {
            $statement = $connection->prepare('SELECT id FROM `menuitem` WHERE name = :name AND tenant_id = :tenant_id AND id != :exclude_id AND deleted_at IS NULL LIMIT 1');
            $statement->execute([
                'name' => $name,
                'tenant_id' => $tenantId,
                'exclude_id' => $excludeId
            ]);
            return (bool)$statement->fetch();
        }

        $statement = $connection->prepare('SELECT id FROM `menuitem` WHERE name = :name AND tenant_id = :tenant_id AND deleted_at IS NULL LIMIT 1');
        $statement->execute([
            'name' => $name,
            'tenant_id' => $tenantId
        ]);
        return (bool)$statement->fetch();
    }

    public function create($tenantId, $displayName, $name, $url, $description, $price, $stock)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('INSERT INTO `menuitem` (tenant_id, display_name, name, url, description, price, stock, created_at, updated_at, deleted_at) VALUES (:tenant_id, :display_name, :name, :url, :description, :price, :stock, NOW(), NOW(), NULL)');
        return $statement->execute([
            'tenant_id' => $tenantId,
            'display_name' => $displayName,
            'name' => $name,
            'url' => $url !== '' ? $url : null,
            'description' => $description,
            'price' => $price,
            'stock' => $stock
        ]);
    }

    public function update($id, $tenantId, $displayName, $name, $url, $description, $price, $stock)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `menuitem` SET display_name = :display_name, name = :name, url = :url, description = :description, price = :price, stock = :stock, updated_at = NOW() WHERE id = :id AND tenant_id = :tenant_id AND deleted_at IS NULL');
        return $statement->execute([
            'id' => $id,
            'tenant_id' => $tenantId,
            'display_name' => $displayName,
            'name' => $name,
            'url' => $url !== '' ? $url : null,
            'description' => $description,
            'price' => $price,
            'stock' => $stock
        ]);
    }

    public function softDelete($id, $tenantId)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `menuitem` SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id AND tenant_id = :tenant_id AND deleted_at IS NULL');
        return $statement->execute([
            'id' => $id,
            'tenant_id' => $tenantId
        ]);
    }
}
