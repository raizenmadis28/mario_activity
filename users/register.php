<?php
require_once __DIR__ . '/../includes/layout.php';
require_guest('/index.php');

$error = ''; $error_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = db();
    $u = trim($_POST['username'] ?? '');
    $em = trim($_POST['email'] ?? '');
    $p = $_POST['password'] ?? '';
    $r = $_POST['role'] ?? 'Customer';
    if ($u === '' || $em === '' || $p === '') { $error = 'OHHH!! MAAMMAAMIAA!! Punan lahat ng fields!'; $error_type = 'gameover'; }
    elseif (!filter_var($em, FILTER_VALIDATE_EMAIL)) { $error = 'Hindi valid ang email.'; $error_type = 'gameover'; }
    elseif (strlen($p) < 6) { $error = 'Dapat 6+ characters ang password.'; $error_type = 'gameover'; }
    else {
        $chk = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $chk->bind_param("ss", $u, $em);
        $chk->execute(); $chk->store_result();
        if ($chk->num_rows > 0) { $error = 'GWAHAHAHA!! MERON NA USERNAME/EMAIL NA GINAMIT!'; $error_type = 'bowser'; }
        else {
            $h = password_hash($p, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $u, $em, $h, $r);
            if ($stmt->execute()) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $u;
                $_SESSION['email'] = $em;
                $_SESSION['role'] = $r;
                toast('YEHEYY!! Registered na, ' . $u . '!', 'success');
                redirect('/index.php');
            }
            $error = 'Registration failed: ' . $conn->error; $error_type = 'gameover';
            $stmt->close();
        }
        $chk->close();
    }
}
render_head('Register', '/Mario Wallpaper/Mario-plays.png');
?>
<div class="min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 overflow-hidden">
<div class="bg-gradient-to-r from-green-500 to-emerald-400 p-6 text-center">
<h1 class="text-3xl font-bold text-white drop-shadow-lg">🍄 Register Account 🍄</h1></div>
<form method="POST" class="p-6 space-y-3 text-white">
<?php if ($error): ?>
<div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold flex items-center gap-3">
<?php if ($error_type === 'bowser'): ?>
<img src="/Funny Memes/Bowser.png" alt="Bowser" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
<?php else: ?>
<img src="/Funny Memes/Mario Advance 4 Game-over.png" alt="Game Over" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
<?php endif; ?>
<span class="text-sm"><?= e($error) ?></span>
</div>
<?php endif; ?>
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">Username</label>
<input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg"></div>
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">Email</label>
<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg"></div>
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">Password (min 6)</label>
<input type="password" name="password" required minlength="6" class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg"></div>
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">Role</label>
<select name="role" class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
<option value="Customer">Customer</option><option value="Admin">Admin</option></select></div>
<button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold text-lg border-2 border-yellow-400">✅ Register</button>
<div class="text-center text-sm pt-2 border-t border-slate-600">May account? <a href="login.php" class="text-yellow-300 font-bold hover:underline">Sign In</a></div>
</form></div></div>
<?php render_footer(); ?>