<?php
require __DIR__ . '/../layouts/head.php';
require __DIR__ . '/../layouts/navbar.php';

$hasAvatar = !empty($user['profile_image']) && file_exists(__DIR__ . '/../../' . $user['profile_image']);
$initial   = strtoupper(substr($user['username'] ?? '?', 0, 1));
?>
<div class="w-full max-w-6xl mx-auto p-3 sm:p-4">

  <div class="bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-4 sm:p-6 lg:p-8 text-white my-4 sm:my-6">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
      <h2 class="text-2xl sm:text-3xl font-bold text-yellow-300">✏️ Edit Profile</h2>
      <a href="/index.php"
         class="text-xs sm:text-sm text-yellow-300 hover:text-yellow-100 underline self-start sm:self-auto">
        ← Back to Dashboard
      </a>
    </div>

    <?php if (!empty($message)): ?>
      <div class="px-4 py-3 rounded-lg font-bold mb-5 <?= $message_type === 'success' ? 'bg-green-700/60 border-2 border-green-400 text-green-100' : 'bg-red-700/60 border-2 border-red-400 text-red-100' ?>">
        <?= e($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

      <!-- ══════════ TOP SECTION: AVATAR (1/3) + BASIC INFO (2/3) ══════════ -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

        <!-- LEFT: AVATAR CARD -->
        <div class="lg:col-span-1">
          <div class="flex flex-col items-center gap-3 p-5 bg-slate-900/60 rounded-xl border-2 border-yellow-400/30 h-full">

            <!-- Avatar preview -->
            <div class="relative">
              <?php if ($hasAvatar): ?>
                <img id="avatar-preview"
                     src="/<?= e($user['profile_image']) ?>" alt="Avatar"
                     class="w-28 h-28 sm:w-36 sm:h-36 rounded-full object-cover border-4 border-yellow-400 shadow-lg">
              <?php else: ?>
                <div id="avatar-preview"
                     class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-yellow-500 flex items-center justify-center text-5xl font-bold text-black border-4 border-yellow-400 shadow-lg">
                  <?= e($initial) ?>
                </div>
              <?php endif; ?>
            </div>

            <!-- Upload button -->
            <label class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 active:scale-95 text-black px-5 py-2 rounded-xl font-bold transition inline-block text-center w-full max-w-[200px]">
              📷 Change Avatar
              <input type="file" name="profile_image" accept="image/*" class="hidden" onchange="previewAvatar(event)">
            </label>

            <p class="text-xs text-gray-400 text-center leading-tight">
              JPG, PNG, WEBP<br>max 5MB<br>
              <span class="text-gray-500">(blank = keep current)</span>
            </p>

          </div>
        </div>

        <!-- RIGHT: BASIC INFO FIELDS -->
        <div class="lg:col-span-2">
          <div class="space-y-4">

            <div>
              <label class="block font-bold text-yellow-300 mb-1 text-sm">Username *</label>
              <input type="text" name="username" value="<?= e($user['username']) ?>" required
                     class="w-full px-3 py-2.5 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:border-yellow-300 focus:outline-none">
            </div>

            <div>
              <label class="block font-bold text-yellow-300 mb-1 text-sm">Email *</label>
              <input type="email" name="email" value="<?= e($user['email']) ?>" required
                     class="w-full px-3 py-2.5 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:border-yellow-300 focus:outline-none">
            </div>

            <div>
              <label class="block font-bold text-yellow-300 mb-1 text-sm">Full Name</label>
              <input type="text" name="full_name" value="<?= e($user['full_name'] ?? '') ?>"
                     placeholder="Juan Dela Cruz"
                     class="w-full px-3 py-2.5 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:border-yellow-300 focus:outline-none">
            </div>

            <div>
              <label class="block font-bold text-yellow-300 mb-1 text-sm">Address</label>
              <input type="text" name="address" value="<?= e($user['address'] ?? '') ?>"
                     placeholder="City, Province"
                     class="w-full px-3 py-2.5 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:border-yellow-300 focus:outline-none">
            </div>

            <?php if ($is_admin): ?>
              <div>
                <label class="block font-bold text-yellow-300 mb-1 text-sm">Role (Admin only)</label>
                <select name="role"
                        class="w-full px-3 py-2.5 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:border-yellow-300 focus:outline-none">
                  <option value="Customer" <?= strcasecmp($user['role'], 'Customer') === 0 ? 'selected' : '' ?>>Customer</option>
                  <option value="Admin"    <?= strcasecmp($user['role'], 'Admin') === 0 ? 'selected' : '' ?>>Admin</option>
                </select>
              </div>
            <?php endif; ?>

          </div>
        </div>

      </div>

      <!-- ══════════ PASSWORD SECTION (FULL WIDTH) ══════════ -->
      <div class="mt-8 pt-6 border-t-2 border-yellow-400/30">
        <h3 class="text-lg sm:text-xl font-bold text-yellow-300 mb-1">🔒 Change Password</h3>
        <p class="text-xs text-gray-400 mb-4">Iwanang blanko kung hindi mo gustong palitan.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold text-yellow-300 mb-1 text-sm">New Password</label>
            <input type="password" name="new_password" minlength="6" autocomplete="new-password"
                   placeholder="Blanko kung hindi papalitan"
                   class="w-full px-3 py-2.5 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:border-yellow-300 focus:outline-none">
          </div>

          <div>
            <label class="block font-bold text-yellow-300 mb-1 text-sm">Confirm New Password</label>
            <input type="password" name="confirm_password" minlength="6" autocomplete="new-password"
                   placeholder="I-ulit ang bagong password"
                   class="w-full px-3 py-2.5 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg focus:border-yellow-300 focus:outline-none">
          </div>
        </div>

        <p class="text-xs text-gray-400 mt-2">⚠️ Dapat magtugma ang New Password at Confirm.</p>
      </div>

      <!-- ══════════ ACTION BUTTONS ══════════ -->
      <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t-2 border-yellow-400/30">
        <button type="submit"
                class="flex-1 bg-green-600 hover:bg-green-700 active:scale-95 text-white py-3 rounded-xl font-bold border-2 border-yellow-400 transition">
          💾 Save Changes
        </button>
        <a href="/index.php"
           class="flex-1 bg-gray-500 hover:bg-gray-600 active:scale-95 text-white py-3 rounded-xl font-bold border-2 border-gray-700 transition text-center">
          ❌ Cancel
        </a>
      </div>

    </form>

  </div>
</div>

<script>
function previewAvatar(event) {
  const file = event.target.files[0];
  if (!file) return;
  const preview = document.getElementById('avatar-preview');
  const reader = new FileReader();
  reader.onload = function(e) {
    if (preview.tagName === 'IMG') {
      preview.src = e.target.result;
    } else {
      const newImg = document.createElement('img');
      newImg.id = 'avatar-preview';
      newImg.className = 'w-28 h-28 sm:w-36 sm:h-36 rounded-full object-cover border-4 border-yellow-400 shadow-lg';
      newImg.src = e.target.result;
      preview.replaceWith(newImg);
    }
  };
  reader.readAsDataURL(file);
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>