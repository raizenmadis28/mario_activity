<?php require __DIR__ . '/../layouts/head.php'; require __DIR__ . '/../layouts/navbar.php'; ?>
<div class="max-w-7xl mx-auto p-4 mt-6">

  <!-- HEADER -->
  <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-3xl shadow-mario border-4 border-black p-6 text-white">
    <h1 class="text-3xl font-bold text-center" style="text-shadow:2px 2px #000;">📦 Inventory Monitoring</h1>
    <p class="text-center text-sm text-green-100 mt-1">Real-time stock levels + full movement history</p>
  </div>

  <!-- STATS -->
  <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-6">
    <div class="bg-white/95 rounded-2xl shadow-mario border-4 border-blue-400 p-4 text-center">
      <div class="text-2xl mb-1">🛍️</div>
      <div class="text-xs font-bold text-gray-500 uppercase">Products</div>
      <div class="text-2xl font-bold text-blue-600"><?= (int)$total_products ?></div>
    </div>
    <div class="bg-white/95 rounded-2xl shadow-mario border-4 border-green-400 p-4 text-center">
      <div class="text-2xl mb-1">📦</div>
      <div class="text-xs font-bold text-gray-500 uppercase">Current Stock</div>
      <div class="text-2xl font-bold text-green-600"><?= (int)$total_stock ?></div>
    </div>
    <div class="bg-white/95 rounded-2xl shadow-mario border-4 border-emerald-400 p-4 text-center">
      <div class="text-2xl mb-1">⬆️</div>
      <div class="text-xs font-bold text-gray-500 uppercase">Total In</div>
      <div class="text-2xl font-bold text-emerald-600"><?= (int)$total_in ?></div>
    </div>
    <div class="bg-white/95 rounded-2xl shadow-mario border-4 border-orange-400 p-4 text-center">
      <div class="text-2xl mb-1">⬇️</div>
      <div class="text-xs font-bold text-gray-500 uppercase">Total Sold</div>
      <div class="text-2xl font-bold text-orange-600"><?= (int)$total_out ?></div>
    </div>
    <div class="bg-white/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-4 text-center">
      <div class="text-2xl mb-1">💰</div>
      <div class="text-xs font-bold text-gray-500 uppercase">Total Value</div>
      <div class="text-lg font-bold text-yellow-600">₱<?= number_format($total_value, 0) ?></div>
    </div>
  </div>

  <!-- TABS -->
  <div class="flex gap-2 mt-6 justify-center flex-wrap">
    <button onclick="showTab('current')" id="tab-btn-current"
            class="px-5 py-2 rounded-lg font-bold border-2 bg-blue-600 text-white border-blue-800">
      📊 Current Stock
    </button>
    <button onclick="showTab('history')" id="tab-btn-history"
            class="px-5 py-2 rounded-lg font-bold border-2 bg-white text-gray-700 border-gray-400 hover:bg-gray-50">
      📜 Movement History
    </button>
  </div>

  <!-- TAB: CURRENT STOCK -->
  <div id="tab-current">
    <div class="flex flex-wrap gap-2 mt-4 justify-center">
      <a href="?filter=all" class="px-4 py-2 rounded-lg font-bold border-2 transition <?= $filter === 'all' ? 'bg-blue-600 text-white border-blue-800' : 'bg-white text-blue-600 border-blue-400' ?>">🔍 All (<?= (int)$total_products ?>)</a>
      <a href="?filter=low" class="px-4 py-2 rounded-lg font-bold border-2 transition <?= $filter === 'low' ? 'bg-yellow-500 text-black border-yellow-700' : 'bg-white text-yellow-600 border-yellow-400' ?>">⚠️ Low (<?= (int)$low_count ?>)</a>
      <a href="?filter=out" class="px-4 py-2 rounded-lg font-bold border-2 transition <?= $filter === 'out' ? 'bg-red-600 text-white border-red-800' : 'bg-white text-red-600 border-red-400' ?>">❌ Out (<?= (int)$out_count ?>)</a>
    </div>

    <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-4 overflow-hidden">
      <div class="bg-gradient-to-r from-green-600 to-green-500 p-4">
        <h2 class="text-2xl font-bold text-white text-center">📊 Stock Status</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-green-200">
            <tr>
              <th class="px-4 py-3 text-left text-sm">Image</th>
              <th class="px-4 py-3 text-left text-sm">Product</th>
              <th class="px-4 py-3 text-left text-sm">Category</th>
              <th class="px-4 py-3 text-left text-sm">Price</th>
              <th class="px-4 py-3 text-center text-sm">Stock</th>
              <th class="px-4 py-3 text-center text-sm">Status</th>
              <th class="px-4 py-3 text-center text-sm">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($rows)): ?>
              <tr><td colspan="7" class="text-center py-12 text-gray-500 font-bold">Walang products.</td></tr>
            <?php else: foreach ($rows as $r):
                $stock = (int)$r['stock'];
                if ($stock === 0)     { $badge = 'bg-red-100 text-red-800 border-red-400'; $text = '❌ Out of Stock'; $row = 'hover:bg-red-50'; }
                elseif ($stock < 5)   { $badge = 'bg-yellow-100 text-yellow-800 border-yellow-400'; $text = '⚠️ Low Stock'; $row = 'hover:bg-yellow-50'; }
                else                  { $badge = 'bg-green-100 text-green-800 border-green-400'; $text = '✓ In Stock'; $row = 'hover:bg-green-50'; }
            ?>
              <tr class="border-t border-green-200 <?= $row ?>">
                <td class="px-4 py-3">
                  <?php if (!empty($r['product_image']) && file_exists(__DIR__ . '/../../' . $r['product_image'])): ?>
                    <img src="/<?= e($r['product_image']) ?>" class="w-12 h-12 object-cover rounded-lg border-2 border-gray-400">
                  <?php else: ?>
                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">🛍️</div>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 font-bold text-green-700"><?= e($r['product_name']) ?></td>
                <td class="px-4 py-3 text-sm"><?= e($r['category']) ?> / <?= e($r['subcategory']) ?></td>
                <td class="px-4 py-3 font-bold text-green-600">₱<?= number_format((float)$r['price'], 2) ?></td>
                <td class="px-4 py-3 text-center">
                  <span class="text-2xl font-bold <?= $stock === 0 ? 'text-red-600' : ($stock < 5 ? 'text-yellow-600' : 'text-green-600') ?>"><?= $stock ?></span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="px-3 py-1 rounded-full text-xs font-bold border-2 <?= $badge ?>"><?= $text ?></span>
                </td>
                <td class="px-4 py-3 text-center">
                  <a href="/products.php?edit=<?= (int)$r['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm font-bold">✏️ Restock / Edit</a>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TAB: MOVEMENT HISTORY -->
  <div id="tab-history" class="hidden">
    <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-4 overflow-hidden">
      <div class="bg-gradient-to-r from-purple-600 to-purple-500 p-4">
        <h2 class="text-2xl font-bold text-white text-center">📜 Movement History (<?= count($history) ?> records)</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-purple-200">
            <tr>
              <th class="px-3 py-3 text-left text-xs">Date/Time</th>
              <th class="px-3 py-3 text-left text-xs">Product</th>
              <th class="px-3 py-3 text-center text-xs">Action</th>
              <th class="px-3 py-3 text-center text-xs">Qty</th>
              <th class="px-3 py-3 text-center text-xs">Old → New</th>
              <th class="px-3 py-3 text-left text-xs">By</th>
              <th class="px-3 py-3 text-left text-xs">Notes</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($history)): ?>
              <tr><td colspan="7" class="text-center py-12 text-gray-500 font-bold">Walang movements pa.</td></tr>
            <?php else: foreach ($history as $h):
                $act = $h['action'];
                if ($act === 'IN')          { $badge = 'bg-emerald-100 text-emerald-800'; $icon = '⬆️ IN'; }
                elseif ($act === 'OUT')     { $badge = 'bg-orange-100 text-orange-800'; $icon = '⬇️ OUT'; }
                elseif ($act === 'ADJUSTMENT'){ $badge = 'bg-yellow-100 text-yellow-800'; $icon = '⚙️ ADJ'; }
                else                        { $badge = 'bg-blue-100 text-blue-800'; $icon = '🎯 INIT'; }
            ?>
              <tr class="border-t border-purple-200 hover:bg-purple-50 text-sm">
                <td class="px-3 py-2 text-xs text-gray-600 whitespace-nowrap"><?= e($h['created_at']) ?></td>
                <td class="px-3 py-2 font-bold"><?= e($h['product_name']) ?></td>
                <td class="px-3 py-2 text-center">
                  <span class="px-2 py-0.5 rounded text-xs font-bold <?= $badge ?>"><?= $icon ?></span>
                </td>
                <td class="px-3 py-2 text-center font-bold"><?= (int)$h['quantity'] ?></td>
                <td class="px-3 py-2 text-center font-mono text-xs">
                  <?= (int)$h['old_stock'] ?> → <strong><?= (int)$h['new_stock'] ?></strong>
                </td>
                <td class="px-3 py-2 text-xs"><?= e($h['user_name'] ?: 'System') ?></td>
                <td class="px-3 py-2 text-xs text-gray-600"><?= e($h['notes']) ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<script>
function showTab(tab) {
  document.getElementById('tab-current').classList.add('hidden');
  document.getElementById('tab-history').classList.add('hidden');
  document.getElementById('tab-' + tab).classList.remove('hidden');

  const curBtn = document.getElementById('tab-btn-current');
  const histBtn = document.getElementById('tab-btn-history');
  if (tab === 'current') {
    curBtn.className = 'px-5 py-2 rounded-lg font-bold border-2 bg-blue-600 text-white border-blue-800';
    histBtn.className = 'px-5 py-2 rounded-lg font-bold border-2 bg-white text-gray-700 border-gray-400 hover:bg-gray-50';
  } else {
    curBtn.className = 'px-5 py-2 rounded-lg font-bold border-2 bg-white text-gray-700 border-gray-400 hover:bg-gray-50';
    histBtn.className = 'px-5 py-2 rounded-lg font-bold border-2 bg-purple-600 text-white border-purple-800';
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>