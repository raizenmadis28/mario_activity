<?php
// config/config.php

// Default para sa live server (Hostinger)
$dbHost = 'localhost';
$dbUser = 'u531130239_mario';
$dbPass = 'Mario2026!@';
$dbName = 'u531130239_mario_db';

// Kung nasa DDEV (local) ka, gagamitin nito ang local settings
if (getenv('IS_DDEV_PROJECT') == 'true' || (isset($_SERVER['SERVER_NAME']) && str_contains($_SERVER['SERVER_NAME'], '.ddev.site'))) {
    $dbHost = 'db';
    $dbUser = 'db';
    $dbPass = 'db';
    $dbName = 'Mario_Activity';
}

return [
    'db' => [
        'host' => $dbHost,
        'user' => $dbUser,
        'pass' => $dbPass,
        'name' => $dbName,
    ],
    'app' => [
        'name'     => 'RaizenMadis28',
        'base_url' => '',
    ],
];