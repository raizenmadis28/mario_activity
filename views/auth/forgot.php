<?php require __DIR__ . '/../layouts/head.php'; ?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-lg bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-8 text-white my-6">
<div class="text-center mb-5">
<div class="flex justify-center gap-4 mb-3">
<img src="/Tools and Guide/toad.png" class="w-20 h-auto rounded" onerror="this.style.display='none'">
<img src="/Tools and Guide/toadette.png" class="w-20 h-auto rounded" onerror="this.style.display='none'">
</div>
<h2 class="text-2xl font-bold text-yellow-300">🍄 Password Recovery</h2>
<p class="text-xs text-gray-300 mt-1">Ilagay ang email para sa 6-digit OTP</p>
</div>
<?php if (!empty($error)): ?>
<div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold mb-4 flex items-center gap-3">
<img src="/Funny Memes/Mario Advance 4 Game-over.png" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
<span class="text-sm"><?= e($error) ?></span>
</div>
<?php endif; ?>
<form method="POST" class="space-y-3">
<div>
<label class="block font-bold text-yellow-300 mb-1 text-sm">Email Address</label>
<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email"
    placeholder="iyong_email@example.com"
    class="w-full px-3 py-3 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
</div>
<button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold border-2 border-yellow-400">
📧 Send 6-Digit Code
</button>
<div class="text-xs text-gray-400 text-center pt-2">
💡 Kung hindi makita, tignan ang <strong class="text-yellow-300">Spam / Junk folder</strong>.
</div>
<div class="text-center text-sm pt-3 border-t border-slate-600">
<a href="login.php" class="text-yellow-300 font-bold hover:underline">🎮 Bumalik sa Login</a>
</div>
</form>
</div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>