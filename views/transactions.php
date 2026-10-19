<?php require __DIR__ . '/layouts/head.php'; require __DIR__ . '/layouts/navbar.php'; ?>
<div class="max-w-6xl mx-auto p-4">

  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-6 overflow-hidden">
    <div class="bg-gradient-to-r from-green-600 to-green-500 p-4">
      <h2 class="text-2xl font-bold text-white text-center">
        <?= $is_admin ? '💳 All Transactions (Admin)' : '💳 My Orders' ?>
      </h2>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-green-200">
          <tr>
            <th class="px-4 py-3 text-left text-sm">ID</th>
            <?php if ($is_admin): ?>
              <th class="px-4 py-3 text-left text-sm">Customer</th>
            <?php endif; ?>
            <th class="px-4 py-3 text-left text-sm">Product</th>
            <th class="px-4 py-3 text-left text-sm">Qty</th>
            <th class="px-4 py-3 text-left text-sm">Price</th>
            <th class="px-4 py-3 text-left text-sm">Total</th>
            <th class="px-4 py-3 text-left text-sm">Payment</th>
            <?php if ($is_admin): ?>
              <th class="px-4 py-3 text-left text-sm">Action</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="<?= $is_admin ? '8' : '6' ?>" class="text-center py-12 text-gray-500 font-bold">
                <?= $is_admin ? 'Walang transactions.' : 'Wala ka pang orders. <a href="/products.php" class="text-pink-600 underline">Mag-shopping na!</a>' ?>
              </td>
            </tr>
          <?php else: foreach ($rows as $t): $total = (float)$t['amount'] * (int)($t['quantity'] ?? 1); ?>
            <tr class="border-t border-green-200 hover:bg-green-50">
              <td class="px-4 py-3 font-bold">#<?= (int)$t['id'] ?></td>
              <?php if ($is_admin): ?>
                <td class="px-4 py-3">
                  <div class="font-bold text-blue-700"><?= e($t['customer_name']) ?></div>
                  <?php if (!empty($t['customer_username'])): ?>
                    <div class="text-xs text-gray-500">@<?= e($t['customer_username']) ?></div>
                  <?php endif; ?>
                </td>
              <?php endif; ?>
              <td class="px-4 py-3"><?= e($t['product_name']) ?></td>
              <td class="px-4 py-3 font-bold">x<?= (int)($t['quantity'] ?? 1) ?></td>
              <td class="px-4 py-3">₱<?= number_format((float)$t['amount'], 2) ?></td>
              <td class="px-4 py-3 font-bold text-green-600">₱<?= number_format($total, 2) ?></td>
              <td class="px-4 py-3">
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">
                  <?= e($t['payment_method']) ?>
                </span>
              </td>
              <?php if ($is_admin): ?>
                <td class="px-4 py-3">
                  <button onclick="confirmLink('?delete=<?= (int)$t['id'] ?>','Burahin itong transaction?')"
                          class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold">
                    Delete
                  </button>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<?php require __DIR__ . '/layouts/footer.php'; ?>