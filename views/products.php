<?php require __DIR__ . '/layouts/head.php'; require __DIR__ . '/layouts/navbar.php'; ?>

<?php if ($is_admin): ?>
<!-- ═══════════════════════ ADMIN VIEW ═══════════════════════ -->
<div class="max-w-6xl mx-auto p-4">

  <!-- ADMIN FORM -->
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">
    <h2 class="text-2xl font-bold text-pink-600 mb-4" style="text-shadow:1px 1px #ffeb3b;">
      <?= $edit ? '✏️ Edit Product' : '🛍️ Add New Product' ?>
    </h2>

    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
      <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <input type="hidden" name="existing_image" value="<?= e($edit['product_image'] ?? '') ?>">
      <?php endif; ?>

      <div>
        <label class="block font-bold mb-1">Product Name</label>
        <input type="text" name="product_name" value="<?= e($edit['product_name'] ?? '') ?>" required
               class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
      </div>

      <div>
        <label class="block font-bold mb-1">Price (₱)</label>
        <input type="number" step="0.01" name="price" value="<?= e($edit['price'] ?? '') ?>" required
               class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
      </div>

      <div>
        <label class="block font-bold mb-1">Stock Quantity</label>
        <input type="number" name="stock" value="<?= e($edit['stock'] ?? 10) ?>" min="0" required
               class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
        <p class="text-xs text-gray-500 mt-1">0 = out of stock, 1-4 = low stock warning</p>
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
        <label class="block font-bold mb-1">📷 Product Image <?= $edit ? '(blank = keep current)' : '' ?></label>
        <input type="file" name="product_image" accept="image/*"
               class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
        <?php if ($edit && !empty($edit['product_image']) && file_exists(__DIR__ . '/../' . $edit['product_image'])): ?>
          <div class="mt-2 flex items-center gap-2">
            <img src="/<?= e($edit['product_image']) ?>" class="w-16 h-16 object-cover rounded border-2 border-gray-400">
            <span class="text-xs text-gray-500">Current image</span>
          </div>
        <?php endif; ?>
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

  <!-- ADMIN TABLE -->
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-6 overflow-hidden">
    <div class="bg-gradient-to-r from-pink-500 to-pink-400 p-4">
      <h2 class="text-2xl font-bold text-white">🛍️ Products (Admin View)</h2>
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
            <th class="px-4 py-3 text-center text-sm">Stock</th>
            <th class="px-4 py-3 text-left text-sm">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
            <tr><td colspan="7" class="text-center py-8 text-gray-500 font-bold">Walang produkto.</td></tr>
          <?php else: foreach ($products as $p):
              $stock = (int)$p['stock'];
              $stockClass = $stock === 0 ? 'text-red-600' : ($stock < 5 ? 'text-yellow-600' : 'text-green-600');
          ?>
            <tr class="border-t border-gray-300 hover:bg-yellow-50">
              <td class="px-4 py-3 font-bold"><?= (int)$p['id'] ?></td>
              <td class="px-4 py-3">
                <?php if (!empty($p['product_image']) && file_exists(__DIR__ . '/../' . $p['product_image'])): ?>
                  <img src="/<?= e($p['product_image']) ?>"
                       class="w-14 h-14 object-cover rounded-lg border-2 border-gray-400">
                <?php else: ?>
                  <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center text-xl">🛍️</div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 font-bold"><?= e($p['product_name']) ?></td>
              <td class="px-4 py-3 text-sm"><?= e($p['category']) ?> / <?= e($p['subcategory']) ?></td>
              <td class="px-4 py-3 font-bold text-green-600">₱<?= number_format((float)$p['price'], 2) ?></td>
              <td class="px-4 py-3 text-center font-bold text-lg <?= $stockClass ?>">
                <?= $stock ?>
              </td>
              <td class="px-4 py-3">
                <a href="?edit=<?= (int)$p['id'] ?>"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm font-bold">Edit</a>
                <button onclick="confirmLink('?delete=<?= (int)$p['id'] ?>','Burahin ang <?= e(addslashes($p['product_name'])) ?>?')"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm font-bold">Delete</button>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php else: ?>
<!-- ═══════════════════════ CUSTOMER VIEW: SHOP GRID ═══════════════════════ -->
<div class="max-w-7xl mx-auto p-4">
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">

    <h1 class="text-3xl font-bold text-pink-600 text-center mb-2" style="text-shadow:2px 2px #ffeb3b;">🛍️ Shop Now</h1>
    <p class="text-center text-gray-600 mb-6">Pumili ng produkto at i-add sa cart</p>

    <?php if (empty($products)): ?>
      <p class="text-center py-12 text-gray-500 font-bold">Walang produkto sa shop.</p>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      <?php foreach ($products as $p):
          $stock      = (int)$p['stock'];
          $inCart     = (int)($cart_quantities[$p['id']] ?? 0);
          $available  = max(0, $stock - $inCart);
          $lowStock   = $available > 0 && $available < 5;
          $allInCart  = ($stock > 0 && $available === 0);
          $outOfStock = ($stock === 0);

          if ($outOfStock) {
              $borderClass = 'border-gray-400';
              $badgeBg     = 'bg-red-100 text-red-800 border-red-400';
              $badgeText   = '❌ OUT OF STOCK';
          } elseif ($allInCart) {
              $borderClass = 'border-purple-400';
              $badgeBg     = 'bg-purple-100 text-purple-800 border-purple-400';
              $badgeText   = '🛒 IN CART';
          } elseif ($lowStock) {
              $borderClass = 'border-yellow-500';
              $badgeBg     = 'bg-yellow-100 text-yellow-800 border-yellow-500';
              $badgeText   = '⚠️ LOW STOCK';
          } else {
              $borderClass = 'border-pink-400 hover:border-pink-600';
              $badgeBg     = 'bg-green-100 text-green-800 border-green-400';
              $badgeText   = '✓ IN STOCK';
          }
      ?>
        <div class="bg-white rounded-2xl shadow-lg border-4 <?= $borderClass ?> overflow-hidden transition-all hover:shadow-2xl flex flex-col relative">

          <!-- STOCK BADGE (top-right) -->
          <div class="absolute top-2 right-2 z-10">
            <span class="px-3 py-1 rounded-full text-xs font-bold border-2 <?= $badgeBg ?> shadow-md">
              <?= $badgeText ?>
            </span>
          </div>

          <!-- IN-CART BADGE (top-left) -->
          <?php if ($inCart > 0): ?>
            <div class="absolute top-2 left-2 z-10">
              <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                🛒 <?= $inCart ?> sa cart
              </span>
            </div>
          <?php endif; ?>

          <!-- PRODUCT IMAGE -->
          <?php if (!empty($p['product_image']) && file_exists(__DIR__ . '/../' . $p['product_image'])): ?>
            <img src="/<?= e($p['product_image']) ?>" class="w-full h-48 object-cover <?= ($outOfStock || $allInCart) ? 'opacity-60 grayscale' : '' ?>">
          <?php else: ?>
            <div class="w-full h-48 bg-gradient-to-br from-pink-100 to-pink-200 flex items-center justify-center text-6xl <?= ($outOfStock || $allInCart) ? 'opacity-60 grayscale' : '' ?>">
              <?= $outOfStock ? '😢' : '🛍️' ?>
            </div>
          <?php endif; ?>

          <div class="p-4 flex flex-col flex-grow">
            <div class="text-xs text-gray-500 font-semibold uppercase tracking-wide">
              <?= e($p['category']) ?> / <?= e($p['subcategory']) ?>
            </div>
            <h3 class="font-bold text-lg text-gray-800 mt-1 leading-tight"><?= e($p['product_name']) ?></h3>
            <?php if (!empty($p['details'])): ?>
              <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= e($p['details']) ?></p>
            <?php endif; ?>

            <div class="mt-3">
              <div class="flex items-baseline justify-between">
                <span class="text-2xl font-bold text-green-600">₱<?= number_format((float)$p['price'], 2) ?></span>
              </div>

              <!-- STOCK QUANTITY LINE (cart-aware) -->
              <div class="mt-2 text-sm font-bold
                  <?= $outOfStock ? 'text-red-600' : ($allInCart ? 'text-purple-600' : ($lowStock ? 'text-yellow-700' : 'text-green-700')) ?>">
                <?php if ($outOfStock): ?>
                  ❌ Out of Stock
                <?php elseif ($allInCart): ?>
                  🛒 Lahat ng <?= $stock ?> nasa cart mo na
                <?php elseif ($inCart > 0): ?>
                  <?= $available ?> available <span class="text-xs text-blue-600">(<?= $inCart ?> nasa cart)</span>
                <?php elseif ($lowStock): ?>
                  ⚠️ Low Stock — Only <?= $available ?> left
                <?php else: ?>
                  ✓ <?= $available ?> available
                <?php endif; ?>
              </div>
            </div>

            <!-- ADD TO CART / DISABLED -->
            <?php if ($available > 0): ?>
              <form method="POST" action="/cart.php" class="mt-3 flex gap-2">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?= $available ?>"
                       class="w-16 px-2 py-2 border-2 border-gray-400 rounded-lg text-center font-bold focus:border-pink-500 focus:outline-none">
                <button type="submit"
                        class="flex-1 bg-pink-500 hover:bg-pink-600 active:scale-95 text-white py-2 rounded-lg font-bold transition text-sm">
                  🛒 Add to Cart
                </button>
              </form>
            <?php elseif ($allInCart): ?>
              <div class="mt-3">
                <a href="/cart.php" class="block w-full bg-purple-500 hover:bg-purple-600 text-white py-2 rounded-lg font-bold text-sm text-center border-2 border-purple-700">
                  🛒 Tingnan ang Cart
                </a>
                <p class="text-xs text-center text-purple-600 mt-1 italic">Lahat ng stock nasa cart mo na</p>
              </div>
            <?php else: ?>
              <div class="mt-3">
                <button type="button" disabled
                        class="w-full bg-gray-300 text-gray-500 py-2 rounded-lg font-bold cursor-not-allowed text-sm border-2 border-gray-400">
                  🚫 Out of Stock
                </button>
                <p class="text-xs text-center text-gray-500 mt-1 italic">Hindi available sa ngayon</p>
              </div>
            <?php endif; ?>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/layouts/footer.php'; ?>