<?php require __DIR__ . '/../layouts/head.php'; ?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-md bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 overflow-hidden my-6">
<div class="bg-gradient-to-r from-red-500 to-orange-400 p-6 text-center">
<h1 class="text-3xl font-bold text-white drop-shadow-lg">🔥 Sign In 🔥</h1></div>
<form method="POST" class="p-6 space-y-4 text-white">
<?php if (!empty($error)): ?>
<div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold flex items-center gap-3">
<img src="<?= $error_type === 'empty' ? '/Funny Memes/Mario-Gameover-Pic.png' : '/Funny Memes/Bowser.png' ?>" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
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
<img src="/Tools and Guide/Green pipe.jpg" class="w-24 h-auto mx-auto rounded-lg border-2 border-white shadow-lg" onerror="this.style.display='none'">
</button>
<p class="text-yellow-300 text-xs mt-2 font-mono">I-click ang Green Pipe para mag-login!</p>
</div>
<div class="flex justify-between text-sm pt-2 border-t border-slate-600">
<a href="register.php" class="text-yellow-300 font-semibold hover:underline">🎮 Register</a>
<a href="forgot_password.php" class="text-pink-300 font-semibold hover:underline">❓ Forgot Password?</a>
</div></form></div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>