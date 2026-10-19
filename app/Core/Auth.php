<?php
namespace App\Core;

class Auth
{
    public static function user(): array
    {
        return [
            'id'       => $_SESSION['user_id']  ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email'    => $_SESSION['email']    ?? null,
            'role'     => $_SESSION['role']     ?? null,
        ];
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return strcasecmp($_SESSION['role'] ?? '', 'Admin') === 0;
    }

    public static function requireLogin(string $to = '/users/login.php'): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/index.php';
            header('Location: ' . $to);
            exit;
        }
    }

    public static function requireGuest(string $to = '/index.php'): void
    {
        if (self::isLoggedIn()) {
            header('Location: ' . $to);
            exit;
        }
    }

    public static function requireAdmin(string $to = '/index.php'): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: ' . $to);
            exit;
        }
    }
}