<?php
require __DIR__ . '/../layouts/head.php';
require __DIR__ . '/../layouts/navbar.php';
?>
<div class="max-w-4xl mx-auto p-4">

  <!-- "VIEWING AS" BANNER -->
  <?php if (!empty($current_admin)): ?>
    <div class="bg-blue-100 border-2 border-blue-400 rounded-2xl p-4 mt-6 flex items-center gap-3">
      <?php if (!empty($current_admin['profile_image']) && file_exists(__DIR__ . '/../../' . $current_admin['profile_image'])): ?>
        <img src="/<?= e($current_admin['profile_image']) ?>"
             class="w-12 h-12 rounded-full object-cover border-2 border-blue-400">
      <?php else: ?>
        <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
          <?= strtoupper(substr($current_admin['username'], 0, 1)) ?>
        </div>
      <?php endif; ?>
      <div class="flex-1">
        <div class="text-sm text-blue-700 font-semibold">👤 Naka-login ka bilang:</div>
        <div class="font-bold text-blue-900 text-lg">
          <?= e($current_admin['username']) ?>
          <span class="text-xs text-blue-600 font-normal">(<?= e($current_admin['email']) ?>)</span>
        </div>
      </div>
      <a href="/users/edit_user.php?id=<?= (int)$current_admin['id'] ?>"
         class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-xs font-bold">
        ✏️ Edit Profile
      </a>
    </div>
    <p class="text-xs text-gray-500 text-center mt-2">
      ℹ️ Hindi ka kasama sa list sa ibaba — kaya hindi mo makikita ang sarili mo.
    </p>
  <?php endif; ?>

  <!-- ADD ADMIN FORM -->
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">
    <h2 class="text-2xl font-bold text-center mb-4" style="color:#ffcc00;text-shadow:2px 2px #000;">↓ ADD NEW ADMIN FORM ↓</h2>
    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <input type="text" name="username" placeholder="Username" required
             class="px-3 py-2 border-2 border-blue-400 rounded-lg">
      <input type="email" name="email" placeholder="Email" required
             class="px-3 py-2 border-2 border-blue-400 rounded-lg">
      <input type="password" name="password" placeholder="Password (min 6)" required minlength="6"
             class="px-3 py-2 border-2 border-blue-400 rounded-lg">
      <button type="submit"
              class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold border-2 border-black md:col-span-3">
        ➕ Add Admin
      </button>
    </form>
  </div>

  <!-- ADMIN LIST (EXCLUDING CURRENT) -->
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-6 overflow-hidden">
    <div class="bg-gradient-to-r from-red-500 to-orange-400 p-4 flex items-center justify-between">
      <h2 class="text-2xl font-bold text-white text-center flex-1">👑 ADMIN LIST</h2>
      <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">
        <?= count($rows) ?> <?= count($rows) === 1 ? 'admin' : 'admins' ?>
      </span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-blue-200">
          <tr>
            <th class="px-4 py-3 text-left text-sm">ID</th>
            <th class="px-4 py-3 text-left text-sm">Avatar</th>
            <th class="px-4 py-3 text-left text-sm">Username</th>
            <th class="px-4 py-3 text-left text-sm">Email</th>
            <th class="px-4 py-3 text-left text-sm">Created</th>
            <th class="px-4 py-3 text-left text-sm">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="6" class="text-center py-8 text-gray-500 font-bold">
                <?php if (count($rows) === 0): ?>
                  Walang ibang admin. Ikaw lang ang naka-registered.
                  <br>
                  <span class="text-xs text-gray-400 font-normal">Mag-add sa form sa itaas para makita mo iba pang admins.</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php else: foreach ($rows as $r): ?>
            <tr class="border-t border-blue-200 hover:bg-blue-50">
              <td class="px-4 py-3 font-bold text-sm">#<?= (int)$r['id'] ?></td>
              <td class="px-4 py-3">
                <?php if (!empty($r['profile_image']) && file_exists(__DIR__ . '/../../' . $r['profile_image'])): ?>
                  <img src="/<?= e($r['profile_image']) ?>"
                       class="w-10 h-10 rounded-full object-cover border-2 border-blue-400">
                <?php else: ?>
                  <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center text-white font-bold text-sm border-2 border-red-700">
                    <?= strtoupper(substr($r['username'], 0, 1)) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 font-bold text-red-700"><?= e($r['username']) ?></td>
              <td class="px-4 py-3 text-sm"><?= e($r['email']) ?></td>
              <td class="px-4 py-3 text-sm text-gray-600"><?= e($r['date_added']) ?></td>
              <td class="px-4 py-3">
                <button onclick="confirmLink('?delete=<?= (int)$r['id'] ?>','Burahin itong admin (<?= e(addslashes($r['username'])) ?>)?')"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-bold">
                  Delete
                </button>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>