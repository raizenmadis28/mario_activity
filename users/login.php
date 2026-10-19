<?php
require_once __DIR__ . '/../includes/layout.php';
require_guest('/index.php');

$error = '';
$error_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = db();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u === '' || $p === '') { $error = 'Punan lahat ng fields!'; $error_type = 'empty'; }
    else {
        $stmt = $conn->prepare("SELECT id,username,password,email,role FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $u, $u);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row && password_verify($p, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            unset($_SESSION['redirect_after_login']); // 🧹 clear stale redirect
            toast('YEHEYY!! Welcome back, ' . $row['username'] . '!', 'success');
            redirect('/index.php'); // 🏠 always land on dashboard
        }
        $error = 'Gwahahaha! Maling password o username!'; $error_type = 'wrong';
    }
}
render_head('Sign In', '/Mario Wallpaper/mario-super-mario-bros-3-wallpaper-preview.jpg');
?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-md bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 overflow-hidden my-6">
<div class="bg-gradient-to-r from-red-500 to-orange-400 p-6 text-center">
<h1 class="text-3xl font-bold text-white drop-shadow-lg">🔥 Sign In 🔥</h1></div>
<form method="POST" class="p-6 space-y-4 text-white">
<?php if ($error): ?>
<div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold flex items-center gap-3">
<?php if ($error_type === 'empty'): ?>
<img src="/Funny Memes/Mario-Gameover-Pic.png" alt="Mario Game Over" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
<?php else: ?>
<img src="/Funny Memes/Bowser.png" alt="Bowser" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
<?php endif; ?>
<span class="text-sm"><?= e($error) ?></span>
</div>
<?php endif; ?>
<div><label class="block font-bold text-yellow-300 mb-1">Username or Email</label>
<input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autocomplete="username"
    class="w-full px-4 py-3 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-300"></div>
<div><label class="block font-bold text-yellow-300 mb-1">Password</label>
<input type="password" name="password" required autocomplete="current-password"
    class="w-full px-4 py-3 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-300"></div>
<div class="text-center">
<button type="submit" class="cursor-pointer transition-transform hover:scale-110" title="Pumasok sa Green Pipe!">
<img src="/Tools and Guide/Green pipe.jpg" alt="Green Pipe Login" class="w-24 h-auto mx-auto rounded-lg border-2 border-white shadow-lg" onerror="this.style.display='none'">
</button>
<p class="text-yellow-300 text-xs mt-2 font-mono">I-click ang Green Pipe para mag-login!</p>
</div>
<div class="flex justify-between text-sm pt-2 border-t border-slate-600">
<a href="register.php" class="text-yellow-300 font-semibold hover:underline">🎮 Register</a>
<a href="forgot_password.php" class="text-pink-300 font-semibold hover:underline">❓ Forgot Password?</a>
</div></form></div></div>
<?php render_footer(); ?>