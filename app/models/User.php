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
                    'role' => $databaseUser['role'] ?? 'user'
                ];
            }

            return false;
        }

        foreach ($this->users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                return [
                    'username' => $user['username'],
                    'role' => $user['role'] ?? 'user'
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

        $statement = $connection->query('SELECT id, username, role FROM `user` ORDER BY id DESC');
        return $statement->fetchAll();
    }

    private function findByUsername($username)
    {
        $connection = $this->database->getConnection();
        if (!$connection) {
            return null;
        }

        $statement = $connection->prepare('SELECT username, password, role FROM `user` WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        return $statement->fetch() ?: null;
    }
}
