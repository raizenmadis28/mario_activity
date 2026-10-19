<?php
namespace App\Core;

class Database
{
    private static ?\mysqli $conn = null;

    public static function conn(): \mysqli
    {
        if (self::$conn === null) {
            $cfg = require __DIR__ . '/../../config/config.php';
            $db  = $cfg['db'];

            self::$conn = new \mysqli($db['host'], $db['user'], $db['pass'], $db['name']);

            if (self::$conn->connect_error) {
                http_response_code(500);
                die('Database connection failed: ' . self::$conn->connect_error);
            }
            self::$conn->set_charset('utf8mb4');
        }
        return self::$conn;
    }
}