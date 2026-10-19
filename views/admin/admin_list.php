<?php
require __DIR__ . '/../layouts/head.php';
require __DIR__ . '/../layouts/navbar.php';
?>
<div class="max-w-4xl mx-auto p-4">
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">
    <h2 class="text-2xl font-bold text-center mb-4" style="color:#ffcc00;text-shadow:2px 2px #000;">↓ ADD NEW ADMIN FORM ↓</h2>
    <form method="POST" class="flex flex-col md:flex-row gap-3">
      <input type="text" name="username" placeholder="Username" required
        class="flex-1 px-3 py-2 border-2 border-blue-400 rounded-lg">
      <input type="password" name="password" placeholder="Password" required
        class="flex-1 px-3 py-2 border-2 border-blue-400 rounded-lg">
      <button type="submit"
        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold border-2 border-black">
        ➕ Add Admin
      </button>
    </form>
  </div>

  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-6 overflow-hidden">
    <div class="bg-gradient-to-r from-red-500 to-orange-400 p-4">
      <h2 class="text-2xl font-bold text-white text-center">👑 ADMIN LIST</h2>
    </div>
    <table class="w-full">
      <thead class="bg-blue-200">
        <tr>
          <th class="px-4 py-3 text-left text-sm">ID</th>
          <th class="px-4 py-3 text-left text-sm">Username</th>
          <th class="px-4 py-3 text-left text-sm">Created</th>
          <th class="px-4 py-3 text-left text-sm">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="4" class="text-center py-8 text-gray-500 font-bold">Walang admins.</td></tr>
        <?php else: foreach ($rows as $i => $r): ?>
          <tr class="border-t border-blue-200 hover:bg-blue-50">
            <td class="px-4 py-3 font-bold">Admin ID: <?= $i + 1 ?></td>
            <td class="px-4 py-3 font-bold text-red-700">| Username: <?= e($r['username']) ?></td>
            <td class="px-4 py-3 text-sm text-gray-600">| Created: <?= e($r['created_at']) ?></td>
            <td class="px-4 py-3">
              <button onclick="confirmLink('?delete=<?= (int)$r['id'] ?>','Burahin itong admin?')"
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
<?php require __DIR__ . '/../layouts/footer.php'; ?>