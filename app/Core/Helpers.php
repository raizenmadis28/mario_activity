<?php
// app/Core/Helpers.php — global helper functions
// Ini-require directly ng bootstrap.php, hindi ito autoloaded (procedural functions)

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('toast')) {
    function toast(string $message, string $type = 'success'): void
    {
        $_SESSION['toast'] = ['msg' => $message, 'type' => $type];
    }
}

if (!function_exists('flash')) {
    function flash(): void
    {
        if (empty($_SESSION['toast'])) return;
        $t = $_SESSION['toast'];
        unset($_SESSION['toast']);
        echo '<script>document.addEventListener("DOMContentLoaded",function(){showToast('
            . json_encode($t['msg']) . ','
            . json_encode($t['type'])
            . ');});</script>';
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}