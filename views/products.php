<?php
// views/products.php — expects: $title, $bg, $products, $edit
require __DIR__ . '/layouts/head.php';
require __DIR__ . '/layouts/navbar.php';
?>
<div class="max-w-6xl mx-auto p-4">

  <!-- ADD / EDIT FORM -->
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">
    <h2 class="text-2xl font-bold text-red-600 mb-4" style="text-shadow:1px 1px #ffeb3b;">
      <?= $edit ? '✏️ Edit Product' : '➕ Add Product' ?>
    </h2>

    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
      <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <input type="hidden" name="existing_image" value="<?= e($edit['product_image'] ?? '') ?>">
      <?php endif; ?>

      <div>
        <label class="block font-bold mb-1">Name</label>
        <input type="text" name="product_name" value="<?= e($edit['product_name'] ?? '') ?>" required
          class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
      </div>

      <div>
        <label class="block font-bold mb-1">Price (₱)</label>
        <input type="number" step="0.01" name="price" value="<?= e($edit['price'] ?? '') ?>" required
          class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
      </div>

      <div>
        <label class="block font-bold mb-1">Category</label>
        <select name="category" required class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
          <?php foreach (['Gundam','Code Geass','Transformers','Valorant','Mobile Legends','Naruto','Call of Duty'] as $c): ?>
            <option value="<?= $c ?>" <?= ($edit['category'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block font-bold mb-1">Subcategory</label>
        <select name="subcategory" required class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
          <?php foreach (['Figures','Manga','Dolls','Keychain','Card Game','Tees'] as $s): ?>
            <option value="<?= $s ?>" <?= ($edit['subcategory'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="block font-bold mb-1">Details</label>
        <textarea name="details" rows="2"
          class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg"><?= e($edit['details'] ?? '') ?></textarea>
      </div>

      <div class="md:col-span-2">
        <label class="block font-bold mb-1">Image <?= $edit ? '(blank = keep current)' : '' ?></label>
        <input type="file" name="product_image" accept="image/*"
          class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
      </div>

      <div class="md:col-span-2 flex gap-2">
        <button type="submit"
          class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold border-2 border-black">
          <?= $edit ? '💾 Update' : '➕ Add' ?>
        </button>
        <?php if ($edit): ?>
          <a href="/products.php"
             class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-bold border-2 border-black">
            Cancel
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- PRODUCTS TABLE -->
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-6 overflow-hidden">
    <div class="bg-gradient-to-r from-pink-500 to-pink-400 p-4">
      <h2 class="text-2xl font-bold text-white">🛍️ Products</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-amber-300">
          <tr>
            <th class="px-4 py-3 text-left text-sm">ID</th>
            <th class="px-4 py-3 text-left text-sm">Image</th>
            <th class="px-4 py-3 text-left text-sm">Name</th>
            <th class="px-4 py-3 text-left text-sm">Category</th>
            <th class="px-4 py-3 text-left text-sm">Price</th>
            <th class="px-4 py-3 text-left text-sm">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
            <tr>
              <td colspan="6" class="text-center py-8 text-gray-500 font-bold">
                Walang produkto. Magdagdag sa form sa itaas.
              </td>
            </tr>
          <?php else: foreach ($products as $p): ?>
            <tr class="border-t border-gray-300 hover:bg-yellow-50">
              <td class="px-4 py-3 font-bold"><?= (int)$p['id'] ?></td>
              <td class="px-4 py-3">
                <?php if (!empty($p['product_image']) && file_exists(__DIR__ . '/../' . $p['product_image'])): ?>
                  <img src="/<?= e($p['product_image']) ?>"
                       class="w-14 h-14 object-cover rounded-lg border-2 border-gray-400">
                <?php else: ?>
                  <span class="text-gray-400 text-xs">—</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 font-bold"><?= e($p['product_name']) ?></td>
              <td class="px-4 py-3"><?= e($p['category']) ?> / <?= e($p['subcategory']) ?></td>
              <td class="px-4 py-3 font-bold text-green-600">₱<?= number_format((float)$p['price'], 2) ?></td>
              <td class="px-4 py-3">
                <a href="?edit=<?= (int)$p['id'] ?>"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm font-bold">
                  Edit
                </a>
                <button onclick="confirmLink('?delete=<?= (int)$p['id'] ?>','Burahin ang <?= e(addslashes($p['product_name'])) ?>?')"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm font-bold">
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
<?php require __DIR__ . '/layouts/footer.php'; ?>