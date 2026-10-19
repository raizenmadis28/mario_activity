<?php
require_once __DIR__ . '/helpers.php';

function is_logged_in(): bool { return isset($_SESSION['user_id']); }
function current_user(): array {
    return ['id'=>$_SESSION['user_id']??null,'username'=>$_SESSION['username']??null,'email'=>$_SESSION['email']??null,'role'=>$_SESSION['role']??null];
}
function require_login(string $to = '/users/login.php'): void {
    if (!is_logged_in()) { $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/index.php'; redirect($to); }
}
function require_guest(string $to = '/index.php'): void { if (is_logged_in()) redirect($to); }
function require_admin(string $to = '/index.php'): void {
    require_login();
    if (strcasecmp($_SESSION['role'] ?? '', 'Admin') !== 0) redirect($to);
}