<?php
require_once __DIR__ . '/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function toast(string $msg, string $type = 'success'): void { $_SESSION['toast'] = ['msg' => $msg, 'type' => $type]; }
function redirect(string $url): void { header("Location: $url"); exit; }
function flash(): void {
    if (!isset($_SESSION['toast'])) return;
    $t = $_SESSION['toast']; unset($_SESSION['toast']);
    echo "<script>document.addEventListener('DOMContentLoaded',()=>showToast(" . json_encode($t['msg']) . "," . json_encode($t['type']) . "));</script>";
}