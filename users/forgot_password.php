<?php
require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/ciphermail.php';
require_guest('/index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = db();
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Ilagay ang email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Hindi valid ang email format.';
    } else {
        // Hanapin ang user by EMAIL ONLY (walang username)
        $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Generate OTP regardless kung existing ang email o hindi
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // I-set ang session (same structure kahit wala ang email)
        $_SESSION['reset_email']       = $email;
        $_SESSION['reset_username']    = $user['username'] ?? '';
        $_SESSION['verification_code'] = $code;
        $_SESSION['code_expires_at']   = time() + 120; // 2 minutes
        $_SESSION['otp_attempts']      = 0;
        $_SESSION['user_exists']       = ($user !== null);

        // Send email LANG kung existing user (silent kung wala — anti-enumeration)
        if ($user) {
            $r = send_otp_email($email, $user['username'], $code);
            $_SESSION['api_status'] = $r['ok'] ? 'sent' : 'failed';
            $_SESSION['api_error']  = $r['ok'] ? '' : ($r['error'] ?? '');
        } else {
            $_SESSION['api_status'] = 'sent'; // fake — huwag ipahalata
            $_SESSION['api_error']  = '';
        }

        // PALAGING parehong toast — kahit hindi registered
        toast('📧 Kung registered ang email, may OTP code doon. Tignan ang Inbox, Spam, at Junk folder.', 'info');
        redirect('/users/verifications.php');
    }
}

render_head('Forgot Password', '/Mario Wallpaper/mario-world-map.jpg');
?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-lg bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-8 text-white my-6">

<div class="text-center mb-5">
<div class="flex justify-center gap-4 mb-3">
<img src="/Tools and Guide/toad.png" alt="Toad" class="w-20 h-auto rounded" onerror="this.style.display='none'">
<img src="/Tools and Guide/toadette.png" alt="Toadette" class="w-20 h-auto rounded" onerror="this.style.display='none'">
</div>
<h2 class="text-2xl font-bold text-yellow-300">🍄 Password Recovery</h2>
<p class="text-xs text-gray-300 mt-1">Ilagay ang iyong email para makakuha ng 6-digit OTP</p>
</div>

<?php if ($error): ?>
<div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold mb-4 flex items-center gap-3">
<img src="/Funny Memes/Mario Advance 4 Game-over.png" alt="Game Over" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
<span class="text-sm"><?= e($error) ?></span>
</div>
<?php endif; ?>

<form method="POST" class="space-y-3">
<div>
<label class="block font-bold text-yellow-300 mb-1 text-sm">Email Address</label>
<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email"
    placeholder="iyong_email@example.com"
    class="w-full px-3 py-3 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-300">
</div>
<button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold border-2 border-yellow-400 transition">
📧 Send 6-Digit Code
</button>
<div class="text-xs text-gray-400 text-center leading-relaxed pt-2">
💡 Kung hindi mo makita ang email, tignan din ang <strong class="text-yellow-300">Spam / Junk folder</strong>.
</div>
<div class="text-center text-sm pt-3 border-t border-slate-600">
<a href="login.php" class="text-yellow-300 font-bold hover:underline">🎮 Bumalik sa Login</a>
</div>
</form>
</div></div>
<?php render_footer(); ?>