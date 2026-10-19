<?php
require __DIR__ . '/../layouts/head.php';
require __DIR__ . '/../layouts/navbar.php';
?>
<div class="max-w-5xl mx-auto p-4">
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">
    <h2 class="text-2xl font-bold text-center text-yellow-400 mb-4" style="text-shadow:2px 2px #000;">↓ ADD NEW INVENTORY ITEM FORM ↓</h2>
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <input type="text" name="product_name" placeholder="Product Name" required class="px-3 py-2 border-2 border-blue-400 rounded-lg">
      <input type="number" step="0.01" name="product_price" placeholder="Price" required class="px-3 py-2 border-2 border-blue-400 rounded-lg">
      <select name="category" class="px-3 py-2 border-2 border-blue-400 rounded-lg">
        <option value="">— Category —</option>
        <?php foreach (['Gundam','Code Geass','Transformers','Valorant','Mobile Legends','Naruto','Call of Duty'] as $c): ?>
          <option><?= $c ?></option>
        <?php endforeach; ?>
      </select>
      <select name="subcategory" class="px-3 py-2 border-2 border-blue-400 rounded-lg">
        <option value="">— Subcategory —</option>
        <?php foreach (['Figures','Manga','Dolls','Keychain','Card Game','Tees'] as $s): ?>
          <option><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      <textarea name="product_details" placeholder="Details" rows="2" class="px-3 py-2 border-2 border-blue-400 rounded-lg md:col-span-2"></textarea>
      <input type="file" name="photo" accept="image/*" class="px-3 py-2 border-2 border-blue-400 rounded-lg md:col-span-2">
      <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold border-2 border-black md:col-span-2">➕ Add Item</button>
    </form>
  </div>

  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-6 overflow-hidden">
    <div class="bg-gradient-to-r from-green-600 to-green-500 p-4">
      <h2 class="text-2xl font-bold text-white text-center">📦 INVENTORY LIST</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-green-200">
          <tr>
            <th class="px-4 py-3 text-left text-sm">ID</th>
            <th class="px-4 py-3 text-left text-sm">Image</th>
            <th class="px-4 py-3 text-left text-sm">Name</th>
            <th class="px-4 py-3 text-left text-sm">Price</th>
            <th class="px-4 py-3 text-left text-sm">Created</th>
            <th class="px-4 py-3 text-left text-sm">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="6" class="text-center py-8 text-gray-500 font-bold">Walang items.</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr class="border-t border-green-200 hover:bg-green-50">
              <td class="px-4 py-3 font-bold">Product ID: <?= (int)$r['id'] ?></td>
              <td class="px-4 py-3">
                <?php if (!empty($r['photo']) && file_exists(__DIR__ . '/../../' . $r['photo'])): ?>
                  <img src="/<?= e($r['photo']) ?>" class="w-14 h-14 object-cover rounded-lg border-2 border-gray-400">
                <?php else: ?>—<?php endif; ?>
              </td>
              <td class="px-4 py-3 font-bold text-green-700"><?= e($r['product_name']) ?></td>
              <td class="px-4 py-3 font-bold text-green-600">₱<?= number_format((float)$r['product_price'], 2) ?></td>
              <td class="px-4 py-3 text-sm"><?= e($r['created_at']) ?></td>
              <td class="px-4 py-3">
                <button onclick="confirmLink('?delete=<?= (int)$r['id'] ?>','Burahin itong item?')"
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