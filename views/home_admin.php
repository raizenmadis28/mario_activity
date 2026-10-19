<?php
require __DIR__ . '/layouts/head.php';
require __DIR__ . '/layouts/navbar.php';
$u = \App\Core\Auth::user();
?>
<div class="max-w-6xl mx-auto p-4">
  <div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 md:p-8 mt-6">
    <h1 class="text-3xl md:text-4xl font-bold text-red-600 text-center mb-2 drop-shadow"
        style="text-shadow:2px 2px #ffeb3b;">👑 Admin Dashboard</h1>
    <p class="text-center text-gray-700 mb-6">
      Maligayang pagdating, <strong><?= e($u['username']) ?></strong> (Admin)!
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <a href="/admin/admin_list.php"
         class="bg-red-50 border-4 border-red-400 rounded-2xl p-6 text-center hover:scale-105 transition-transform shadow-lg">
        <div class="text-5xl mb-2">👑</div>
        <div class="text-xl font-bold text-red-700">Admin List</div>
        <div class="text-xs text-red-600 mt-1">Manage admins</div>
      </a>

      <a href="/admin/customer_list.php"
         class="bg-blue-50 border-4 border-blue-400 rounded-2xl p-6 text-center hover:scale-105 transition-transform shadow-lg">
        <div class="text-5xl mb-2">👤</div>
        <div class="text-xl font-bold text-blue-700">Customer List</div>
        <div class="text-xs text-blue-600 mt-1">Manage customers</div>
      </a>

      <a href="/admin/inventory_list.php"
         class="bg-green-50 border-4 border-green-400 rounded-2xl p-6 text-center hover:scale-105 transition-transform shadow-lg">
        <div class="text-5xl mb-2">📦</div>
        <div class="text-xl font-bold text-green-700">Inventory List</div>
        <div class="text-xs text-green-600 mt-1">Manage inventory</div>
      </a>

      <a href="/products.php"
         class="bg-pink-50 border-4 border-pink-400 rounded-2xl p-6 text-center hover:scale-105 transition-transform shadow-lg">
        <div class="text-5xl mb-2">🛍️</div>
        <div class="text-xl font-bold text-pink-700">Products</div>
        <div class="text-xs text-pink-600 mt-1">Manage products</div>
      </a>

      <a href="/transactions.php"
         class="bg-yellow-50 border-4 border-yellow-400 rounded-2xl p-6 text-center hover:scale-105 transition-transform shadow-lg">
        <div class="text-5xl mb-2">💳</div>
        <div class="text-xl font-bold text-yellow-700">Transactions</div>
        <div class="text-xs text-yellow-600 mt-1">View all transactions</div>
      </a>
    </div>
  </div>
</div>
<?php require __DIR__ . '/layouts/footer.php'; ?>