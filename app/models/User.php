<?php

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Database.php';

class User extends Model
{
    private $users;
    private $database;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->users = $config['users'] ?? [];
        $this->database = new Database();
    }

    public function authenticate($username, $password)
    {
        $databaseUser = $this->findByUsername($username);
        if ($databaseUser) {
            $storedPassword = $databaseUser['password'] ?? '';
            $isPasswordValid = password_verify($password, $storedPassword) || $password === $storedPassword;

            if ($isPasswordValid) {
                return [
                    'username' => $databaseUser['username'],
                    'role' => $databaseUser['role'] ?? 'user',
                    'tenant_id' => $databaseUser['tenant_id'] ?? null,
                    'tenant_name' => $databaseUser['tenant_name'] ?? null
                ];
            }

            return false;
        }

        foreach ($this->users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                return [
                    'username' => $user['username'],
                    'role' => $user['role'] ?? 'user',
                    'tenant_id' => $user['tenant_id'] ?? null,
                    'tenant_name' => $user['tenant_name'] ?? null
                ];
            }
        }

        return false;
    }

    public function getAllUsers()
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return [];
        }

        $statement = $connection->query('SELECT u.id, u.tenant_id, t.display_name AS tenant_display_name, u.username, u.email, u.role, u.created_at, u.updated_at FROM `user` u INNER JOIN `tenant` t ON t.id = u.tenant_id WHERE u.deleted_at IS NULL ORDER BY u.id DESC');
        return $statement->fetchAll();
    }

    public function findById($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return null;
        }

        $statement = $connection->prepare('SELECT id, tenant_id, username, email, role, created_at, updated_at FROM `user` WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function usernameExists($username, $excludeId = null)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        if ($excludeId !== null) {
            $statement = $connection->prepare('SELECT id FROM `user` WHERE username = :username AND id != :exclude_id AND deleted_at IS NULL LIMIT 1');
            $statement->execute([
                'username' => $username,
                'exclude_id' => $excludeId
            ]);

            return (bool)$statement->fetch();
        }

        $statement = $connection->prepare('SELECT id FROM `user` WHERE username = :username AND deleted_at IS NULL LIMIT 1');
        $statement->execute(['username' => $username]);
        return (bool)$statement->fetch();
    }

    public function emailExists($email, $excludeId = null)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        if ($excludeId !== null) {
            $statement = $connection->prepare('SELECT id FROM `user` WHERE email = :email AND id != :exclude_id AND deleted_at IS NULL LIMIT 1');
            $statement->execute([
                'email' => $email,
                'exclude_id' => $excludeId
            ]);

            return (bool)$statement->fetch();
        }

        $statement = $connection->prepare('SELECT id FROM `user` WHERE email = :email AND deleted_at IS NULL LIMIT 1');
        $statement->execute(['email' => $email]);
        return (bool)$statement->fetch();
    }

    public function createUser($tenantId, $username, $email, $password, $role)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $statement = $connection->prepare('INSERT INTO `user` (tenant_id, username, email, password, role, created_at, updated_at, deleted_at) VALUES (:tenant_id, :username, :email, :password, :role, NOW(), NULL, NULL)');
        return $statement->execute([
            'tenant_id' => $tenantId,
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);
    }

    public function updateUser($id, $tenantId, $username, $email, $role, $password = '')
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        if ($password !== '') {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $statement = $connection->prepare('UPDATE `user` SET tenant_id = :tenant_id, username = :username, email = :email, role = :role, password = :password, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
            return $statement->execute([
                'id' => $id,
                'tenant_id' => $tenantId,
                'username' => $username,
                'email' => $email,
                'role' => $role,
                'password' => $hashedPassword
            ]);
        }

        $statement = $connection->prepare('UPDATE `user` SET tenant_id = :tenant_id, username = :username, email = :email, role = :role, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $statement->execute([
            'id' => $id,
            'tenant_id' => $tenantId,
            'username' => $username,
            'email' => $email,
            'role' => $role
        ]);
    }

    public function deleteUser($id)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return false;
        }

        $statement = $connection->prepare('UPDATE `user` SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $statement->execute(['id' => $id]);
    }

    private function findByUsername($username)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return null;
        }

        $statement = $connection->prepare('SELECT u.tenant_id, t.name as tenant_name, u.username, u.password, u.role FROM `user` u JOIN `tenant` t ON u.tenant_id = t.id WHERE u.username = :username AND u.deleted_at IS NULL LIMIT 1');
        $statement->execute(['username' => $username]);
        return $statement->fetch() ?: null;
    }
}
