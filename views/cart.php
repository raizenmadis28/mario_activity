<?php require __DIR__ . '/layouts/head.php'; require __DIR__ . '/layouts/navbar.php'; ?>
<div class="max-w-5xl mx-auto p-4">

  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">
    <h1 class="text-3xl font-bold text-pink-600 text-center mb-6" style="text-shadow:2px 2px #ffeb3b;">🛒 My Cart</h1>

    <?php if (empty($items)): ?>
      <div class="text-center py-12">
        <div class="text-6xl mb-4">🛒</div>
        <p class="text-gray-500 font-bold text-lg">Walang laman ang cart mo.</p>
        <a href="/products.php" class="inline-block mt-4 bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-xl font-bold">
          🛍️ Mag-shopping
        </a>
      </div>
    <?php else: ?>

    <!-- CART ITEMS TABLE -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-pink-200">
          <tr>
            <th class="px-4 py-3 text-left text-sm">Product</th>
            <th class="px-4 py-3 text-left text-sm">Price</th>
            <th class="px-4 py-3 text-left text-sm">Quantity</th>
            <th class="px-4 py-3 text-left text-sm">Subtotal</th>
            <th class="px-4 py-3 text-left text-sm">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr class="border-t border-pink-200">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <?php if (!empty($it['image']) && file_exists(__DIR__ . '/../' . $it['image'])): ?>
                    <img src="/<?= e($it['image']) ?>" class="w-14 h-14 object-cover rounded-lg border-2 border-pink-300">
                  <?php else: ?>
                    <div class="w-14 h-14 bg-pink-100 rounded-lg flex items-center justify-center text-2xl">🛍️</div>
                  <?php endif; ?>
                  <div>
                    <div class="font-bold"><?= e($it['name']) ?></div>
                    <div class="text-xs text-gray-500"><?= e($it['category']) ?></div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 font-bold text-green-600">₱<?= number_format($it['price'], 2) ?></td>
              <td class="px-4 py-3">
                <form method="POST" action="/cart.php" class="flex gap-1 items-center">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="product_id" value="<?= (int)$it['id'] ?>">
                  <input type="number" name="quantity" value="<?= (int)$it['qty'] ?>" min="1" max="<?= (int)$it['stock'] ?>"
                         class="w-16 px-2 py-1 border-2 border-gray-400 rounded-lg text-center font-bold">
                  <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold">Update</button>
                </form>
              </td>
              <td class="px-4 py-3 font-bold text-green-600">₱<?= number_format($it['subtotal'], 2) ?></td>
              <td class="px-4 py-3">
                <form method="POST" action="/cart.php">
                  <input type="hidden" name="action" value="remove">
                  <input type="hidden" name="product_id" value="<?= (int)$it['id'] ?>">
                  <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold">Remove</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-pink-100">
          <tr>
            <td colspan="3" class="px-4 py-4 text-right font-bold text-lg">TOTAL:</td>
            <td colspan="2" class="px-4 py-4 font-bold text-2xl text-green-600">₱<?= number_format($total, 2) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- CHECKOUT FORM -->
    <div class="mt-8 bg-yellow-50 border-4 border-yellow-400 rounded-2xl p-6">
      <h2 class="text-2xl font-bold text-yellow-700 mb-4">💳 Checkout</h2>

      <form method="POST" action="/cart.php" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="action" value="checkout">

        <!-- Full Name (auto-filled, editable) -->
        <div class="md:col-span-2">
          <label class="block font-bold mb-1">
            Full Name *
            <span class="text-xs font-normal text-blue-600">(auto-filled from profile — pwede mong palitan)</span>
          </label>
          <input type="text" name="customer_name" value="<?= e($default_name) ?>" required
                 placeholder="Ilagay ang pangalan"
                 class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:border-blue-500 focus:outline-none">
          <?php if (!empty($default_name)): ?>
            <p class="text-xs text-gray-500 mt-1">💡 Kung iba ang tatanggap, palitan lang sa taas.</p>
          <?php endif; ?>
        </div>

        <!-- Delivery Address (auto-filled, editable) -->
        <div class="md:col-span-2">
          <label class="block font-bold mb-1">
            Delivery Address *
            <span class="text-xs font-normal text-blue-600">(auto-filled from profile — pwede mong palitan)</span>
          </label>
          <textarea name="delivery_address" rows="2" required
                    placeholder="Ilagay ang delivery address"
                    class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:border-blue-500 focus:outline-none"><?= e($default_addr) ?></textarea>
          <?php if (empty($default_addr)): ?>
            <p class="text-xs text-orange-600 mt-1">⚠️ Walang address sa profile mo — ilagay manually.</p>
          <?php endif; ?>
        </div>

        <!-- Payment Method -->
        <div class="md:col-span-2">
          <label class="block font-bold mb-1">Payment Method *</label>
          <select name="payment_method" required class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
            <option value="Cash on Delivery (COD)">Cash on Delivery (COD)</option>
            <option value="GCash">GCash</option>
            <option value="Maya">Maya</option>
            <option value="Bank Transfer">Bank Transfer</option>
          </select>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="md:col-span-2 flex gap-3">
          <button type="submit"
                  class="flex-1 bg-green-600 hover:bg-green-700 active:scale-95 text-white py-3 rounded-xl font-bold text-lg border-2 border-black transition">
            ✅ Place Order — ₱<?= number_format($total, 2) ?>
          </button>

          <button type="button"
                  onclick="confirmClearCart()"
                  class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-3 rounded-xl font-bold">
            🗑️ Clear
          </button>
        </div>
      </form>
    </div>

    <?php endif; ?>
  </div>
</div>

<!-- SEPARATE FORM para sa Clear -->
<form id="clear-cart-form" method="POST" action="/cart.php" class="hidden">
  <input type="hidden" name="action" value="clear">
</form>

<script>
function confirmClearCart() {
  confirmDialog('Sigurado ka bang gusto mong i-clear ang cart?', () => {
    document.getElementById('clear-cart-form').submit();
  });
}
</script>

<?php require __DIR__ . '/layouts/footer.php'; ?>