<?php
require_once __DIR__ . '/../includes/layout.php';
require_guest('/index.php');

// Triple guard: kailangan may reset_email, verified_success, AT user_exists
if (
    !isset($_SESSION['reset_email']) ||
    empty($_SESSION['verified_success']) ||
    empty($_SESSION['user_exists'])
) {
    redirect('/users/forgot_password.php');
}

$conn = db();
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new  = $_POST['new_password'] ?? '';
    $conf = $_POST['confirm_password'] ?? '';

    if (empty($new) || empty($conf)) {
        $message = 'Punan ang password fields!';
        $message_type = 'error';
    } elseif ($new !== $conf) {
        $message = 'Hindi nagtutugma ang mga password!';
        $message_type = 'error';
    } else {
        $len = strlen($new);
        $num = preg_match('/[0-9]/', $new);
        $chr = preg_match('/[a-zA-Z]/', $new);
        $sym = preg_match('/[\W_]/', $new);

        if ($len < 6)               { $message = 'VERY WEAK password! (min 6 chars)'; $message_type = 'error'; }
        elseif ($len < 8)            { $message = 'WEAK password! (need 8+ chars)'; $message_type = 'error'; }
        elseif (!$num||!$chr||!$sym) { $message = 'TOO WEAK! Kailangan letters + numbers + symbols.'; $message_type = 'error'; }
        else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $email = $_SESSION['reset_email'];
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
            $stmt->bind_param("ss", $hash, $email);
            $stmt->execute();
            $stmt->close();

            // Clear ALL reset session vars
            unset(
                $_SESSION['reset_email'], $_SESSION['reset_username'],
                $_SESSION['verification_code'], $_SESSION['code_expires_at'],
                $_SESSION['verified_success'], $_SESSION['api_status'],
                $_SESSION['api_error'], $_SESSION['otp_attempts'],
                $_SESSION['user_exists']
            );
            toast('YEHEYY!! Nareset na ang password. Mag-login gamit ang bago.', 'success');
            redirect('/users/login.php');
        }
    }
}

render_head('Reset Password', '/Mario Wallpaper/SuperStar_Road.png');
?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-lg bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-8 text-white text-center my-6">

<div class="flex justify-center gap-4 mb-4">
<img src="/Tools and Guide/Daisy.jpg" alt="Daisy" class="w-16 h-auto rounded" onerror="this.style.display='none'">
<h2 class="text-2xl font-bold text-yellow-300 self-center">👑 Reset Password 💖</h2>
<img src="/Tools and Guide/peach.jpg" alt="Peach" class="w-16 h-auto rounded" onerror="this.style.display='none'">
</div>

<?php if ($message): ?>
<div class="px-4 py-3 rounded-lg font-bold mb-4 <?= $message_type==='success' ? 'bg-green-700/60 border-2 border-green-400 text-green-100' : 'bg-red-700/60 border-2 border-red-400 text-red-100' ?>">
<?= e($message) ?>
</div>
<?php endif; ?>

<form method="POST" class="space-y-3 text-left">
<div>
<label class="block font-bold text-yellow-300 mb-1 text-sm">New Password</label>
<input type="password" name="new_password" required autocomplete="new-password"
    class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
</div>
<div>
<label class="block font-bold text-yellow-300 mb-1 text-sm">Confirm New Password</label>
<input type="password" name="confirm_password" required autocomplete="new-password"
    class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
</div>
<div class="text-xs text-gray-300 text-center">Kailangan 8+ chars, may letter, number, at symbol.</div>
<button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold border-2 border-yellow-400 transition">
💾 Save New Password
</button>
</form>

<div class="text-center text-sm pt-4 border-t border-slate-600 mt-5">
<a href="login.php" class="text-yellow-300 font-bold hover:underline">🎮 Bumalik sa Login</a>
</div>
</div></div>
<?php render_footer(); ?>