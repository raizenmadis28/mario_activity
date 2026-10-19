<?php require __DIR__ . '/../layouts/head.php'; require __DIR__ . '/../layouts/navbar.php'; ?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-md bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-8 text-white my-6">
<h2 class="text-2xl font-bold text-yellow-300 text-center mb-4">✏️ Edit User Account</h2>
<?php if (!empty($message)): ?>
<div class="px-4 py-3 rounded-lg font-bold mb-4 <?= $message_type === 'success' ? 'bg-green-700/60 border-2 border-green-400 text-green-100' : 'bg-red-700/60 border-2 border-red-400 text-red-100' ?>">
<?= e($message) ?>
</div>
<?php endif; ?>
<form method="POST" class="space-y-3">
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">Username</label>
<input type="text" name="username" value="<?= e($user['username']) ?>" required class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg"></div>
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">Email</label>
<input type="email" name="email" value="<?= e($user['email']) ?>" required class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg"></div>
<div><label class="block font-bold text-yellow-300 mb-1 text-sm">Role</label>
<select name="role" <?= $is_admin ? '' : 'disabled' ?> class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
<option value="Customer" <?= strcasecmp($user['role'],'Customer')===0 ? 'selected' : '' ?>>Customer</option>
<option value="Admin" <?= strcasecmp($user['role'],'Admin')===0 ? 'selected' : '' ?>>Admin</option></select></div>
<button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold border-2 border-yellow-400">💾 Update Account</button>
<div class="text-center text-sm pt-3 border-t border-slate-600"><a href="/index.php" class="text-yellow-300 font-bold hover:underline">🎮 Bumalik sa Dashboard</a></div>
</form>
</div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>