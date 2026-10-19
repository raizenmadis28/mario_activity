<?php require __DIR__ . '/../layouts/head.php'; ?>
<div class="flex-1 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 overflow-hidden my-6">

    <div class="bg-gradient-to-r from-green-500 to-emerald-400 p-6 text-center">
      <h1 class="text-3xl font-bold text-white drop-shadow-lg">🍄 Register Account 🍄</h1>
      <p class="text-xs text-white/80 mt-1">Customer Account</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-3 text-white">

      <?php if (!empty($error)): ?>
        <div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold flex items-center gap-3">
          <img src="<?= $error_type === 'bowser' ? '/Funny Memes/Bowser.png' : '/Funny Memes/Mario Advance 4 Game-over.png' ?>"
               alt="" class="w-12 h-auto flex-shrink-0" onerror="this.style.display='none'">
          <span class="text-sm"><?= e($error) ?></span>
        </div>
      <?php endif; ?>

      <!-- PROFILE IMAGE (required) -->
      <div>
        <label class="block font-bold text-yellow-300 mb-1 text-sm">📷 Profile Picture *</label>
        <input type="file" name="profile_image" accept="image/*" required
               class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:bg-yellow-500 file:text-black file:font-bold file:cursor-pointer">
        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — max 5MB</p>
      </div>

      <!-- FULL NAME (required) -->
      <div>
        <label class="block font-bold text-yellow-300 mb-1 text-sm">Full Name *</label>
        <input type="text" name="full_name" value="<?= e($_POST['full_name'] ?? '') ?>" required
               placeholder="Juan Dela Cruz"
               class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
      </div>

      <!-- USERNAME (required) -->
      <div>
        <label class="block font-bold text-yellow-300 mb-1 text-sm">Username *</label>
        <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autocomplete="username"
               class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
      </div>

      <!-- EMAIL (required) -->
      <div>
        <label class="block font-bold text-yellow-300 mb-1 text-sm">Email *</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email"
               class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
      </div>

      <!-- ADDRESS (required) -->
      <div>
        <label class="block font-bold text-yellow-300 mb-1 text-sm">Address *</label>
        <input type="text" name="address" value="<?= e($_POST['address'] ?? '') ?>" required
               placeholder="City, Province"
               class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
      </div>

      <!-- PASSWORD (required) -->
      <div>
        <label class="block font-bold text-yellow-300 mb-1 text-sm">Password * <span class="text-xs font-normal text-gray-400">(min 6)</span></label>
        <input type="password" name="password" required minlength="6" autocomplete="new-password"
               class="w-full px-3 py-2 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg">
      </div>

      <button type="submit"
              class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold text-lg border-2 border-yellow-400 transition">
        ✅ Register
      </button>

      <div class="text-center text-sm pt-2 border-t border-slate-600">
        May account? <a href="login.php" class="text-yellow-300 font-bold hover:underline">Sign In</a>
      </div>

    </form>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>