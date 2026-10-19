<?php require __DIR__ . '/../layouts/head.php'; ?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-md bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 overflow-hidden my-6">
<div class="bg-gradient-to-r from-green-500 to-emerald-400 p-6 text-center">
<h1 class="text-3xl font-bold text-white drop-shadow-lg">🍄 Register Account 🍄</h1></div>
<form method="POST" class="p-6 space-y-3 text-white">
<?php if (!empty($error)): ?>
<div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold flex items-center gap-3">
<img src="<?= $error_type === 'bowser' ? '/Funny Memes/Bowser.png' : '/Funny Memes/Mario Advance 4 Game-over.png' ?>" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
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
<?php require __DIR__ . '/../layouts/footer.php'; ?>