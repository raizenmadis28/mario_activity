<?php require __DIR__ . '/layouts/head.php'; require __DIR__ . '/layouts/navbar.php';
$u = \App\Core\Auth::user();
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<div class="max-w-6xl mx-auto p-4">
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 md:p-8 mt-6">
    <h1 class="text-3xl md:text-4xl font-bold text-blue-600 text-center mb-2 drop-shadow"
        style="text-shadow:2px 2px #ffeb3b;">🛍️ Customer Dashboard</h1>
    <p class="text-center text-gray-700 mb-6">
      Maligayang pagdating, <strong><?= e($u['username']) ?></strong>!
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <a href="/products.php"
         class="bg-pink-50 border-4 border-pink-400 rounded-2xl p-6 text-center hover:scale-105 transition-transform shadow-lg">
        <div class="text-5xl mb-2">🛍️</div>
        <div class="text-xl font-bold text-pink-700">Shop Products</div>
        <div class="text-xs text-pink-600 mt-1">Browse and buy</div>
      </a>

      <a href="/cart.php"
         class="bg-yellow-50 border-4 border-yellow-400 rounded-2xl p-6 text-center hover:scale-105 transition-transform shadow-lg relative">
        <?php if ($cartCount > 0): ?>
          <span class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-full w-7 h-7 flex items-center justify-center font-bold">
            <?= (int)$cartCount ?>
          </span>
        <?php endif; ?>
        <div class="text-5xl mb-2">🛒</div>
        <div class="text-xl font-bold text-yellow-700">My Cart</div>
        <div class="text-xs text-yellow-600 mt-1"><?= $cartCount > 0 ? "{$cartCount} items" : 'Walang laman' ?></div>
      </a>

      <a href="/transactions.php"
         class="bg-blue-50 border-4 border-blue-400 rounded-2xl p-6 text-center hover:scale-105 transition-transform shadow-lg">
        <div class="text-5xl mb-2">💳</div>
        <div class="text-xl font-bold text-blue-700">My Orders</div>
        <div class="text-xs text-blue-600 mt-1">Purchase history</div>
      </a>
    </div>
  </div>
</div>
<?php require __DIR__ . '/layouts/footer.php'; ?>