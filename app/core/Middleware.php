<?php

class Middleware
{
    private static $roleLevels = [
        'guest' => 0,
        'user' => 1,
        'admin' => 2,
    ];

    public static function handle(array $middleware)
    {
        self::startSession();

        foreach ($middleware as $rule) {
            if ($rule === 'auth') {
                self::requireAuth();
                continue;
            }

            if (str_starts_with($rule, 'role:')) {
                $requiredRole = trim(substr($rule, 5));
                self::requireRole($requiredRole);
            }
        }
    }

    private static function startSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private static function requireAuth()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    private static function requireRole($requiredRole)
    {
        self::requireAuth();

        $currentRole = $_SESSION['role'] ?? 'guest';
        $requiredLevel = self::$roleLevels[$requiredRole] ?? PHP_INT_MAX;
        $currentLevel = self::$roleLevels[$currentRole] ?? -1;

        if ($currentLevel < $requiredLevel) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Forbidden: insufficient authorization level.';
            exit;
        }
    }
}
