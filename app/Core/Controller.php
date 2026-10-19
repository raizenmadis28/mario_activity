<?php
namespace App\Core;

abstract class Controller
{
    protected function view(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $file = __DIR__ . '/../../views/' . $name . '.php';
        if (!is_file($file)) {
            http_response_code(500);
            die('View not found: ' . $name);
        }
        require $file;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}