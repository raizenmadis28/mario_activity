<?php require __DIR__ . '/../layouts/head.php'; ?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-lg bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-8 text-white text-center my-6">
<div class="flex justify-center gap-4 mb-4">
<img src="/Tools and Guide/Daisy.jpg" class="w-16 h-auto rounded" onerror="this.style.display='none'">
<h2 class="text-2xl font-bold text-yellow-300 self-center">👑 Reset Password 💖</h2>
<img src="/Tools and Guide/peach.jpg" class="w-16 h-auto rounded" onerror="this.style.display='none'">
</div>
<?php if (!empty($message)): ?>
<div class="px-4 py-3 rounded-lg font-bold mb-4 <?= $message_type === 'success' ? 'bg-green-700/60 border-2 border-green-400 text-green-100' : 'bg-red-700/60 border-2 border-red-400 text-red-100' ?>">
<?= e($message) ?>
</div>
<?php endif; ?>
<form method="POST" class="space-y-3 text-left">
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">New Password</label>
<input type="password" name="new_password" required autocomplete="new-password" class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg"></div>
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">Confirm New Password</label>
<input type="password" name="confirm_password" required autocomplete="new-password" class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg"></div>
<div class="text-xs text-gray-300 text-center">Kailangan 8+ chars, letter+number+symbol.</div>
<button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold border-2 border-yellow-400">💾 Save New Password</button>
</form>
<div class="text-center text-sm pt-4 border-t border-slate-600 mt-5">
<a href="login.php" class="text-yellow-300 font-bold hover:underline">🎮 Bumalik sa Login</a>
</div>
</div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>