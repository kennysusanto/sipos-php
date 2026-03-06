<?php

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Database.php';

class Tenant extends Model
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getAllTenants()
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return [];
        }

        $statement = $connection->query('SELECT id, name, display_name, created_at, updated_at FROM `tenant` WHERE deleted_at IS NULL ORDER BY id DESC');
        return $statement->fetchAll();
    }

    public function getTenantOptions()
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return [];
        }

        $statement = $connection->query('SELECT id, display_name FROM `tenant` WHERE deleted_at IS NULL ORDER BY display_name ASC');
        return $statement->fetchAll();
    }

    public function findById($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return null;
        }

        $statement = $connection->prepare('SELECT id, name, display_name, created_at, updated_at FROM `tenant` WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function nameExists($name, $excludeId = null)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        if ($excludeId !== null) {
            $statement = $connection->prepare('SELECT id FROM `tenant` WHERE name = :name AND id != :exclude_id AND deleted_at IS NULL LIMIT 1');
            $statement->execute([
                'name' => $name,
                'exclude_id' => $excludeId
            ]);
            return (bool)$statement->fetch();
        }

        $statement = $connection->prepare('SELECT id FROM `tenant` WHERE name = :name AND deleted_at IS NULL LIMIT 1');
        $statement->execute(['name' => $name]);
        return (bool)$statement->fetch();
    }

    public function createTenant($name, $displayName)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('INSERT INTO `tenant` (name, display_name, created_at, updated_at, deleted_at) VALUES (:name, :display_name, NOW(), NULL, NULL)');
        return $statement->execute([
            'name' => $name,
            'display_name' => $displayName
        ]);
    }

    public function updateTenant($id, $name, $displayName)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `tenant` SET name = :name, display_name = :display_name, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $statement->execute([
            'id' => $id,
            'name' => $name,
            'display_name' => $displayName
        ]);
    }

    public function hasActiveUsers($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('SELECT id FROM `user` WHERE tenant_id = :tenant_id AND deleted_at IS NULL LIMIT 1');
        $statement->execute(['tenant_id' => $id]);
        return (bool)$statement->fetch();
    }

    public function deleteTenant($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `tenant` SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $statement->execute(['id' => $id]);
    }
}
