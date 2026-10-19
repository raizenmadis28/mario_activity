<?php
// views/layouts/navbar.php
// Auto-hide kung hindi naka-login
if (!\App\Core\Auth::isLoggedIn()) return;
$u = \App\Core\Auth::user();
?>
<nav class="bg-gradient-to-r from-red-600 to-red-500 text-white shadow-lg border-b-4 border-yellow-400 flex-shrink-0 sticky top-0 z-30">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between flex-wrap gap-3">
    <a href="/index.php" class="text-2xl font-bold hover:scale-105 transition-transform">🍄 RaizenMadis28</a>
    <div class="flex items-center gap-2 flex-wrap text-sm">
      <span class="bg-white/20 px-3 py-1 rounded-full">👤 <?= e($u['username']) ?> <span class="opacity-75">(<?= e($u['role']) ?>)</span></span>
      <a href="/index.php" class="hover:bg-white/20 px-3 py-2 rounded-lg font-semibold transition">🏠 Home</a>
      <a href="/about.php" class="hover:bg-white/20 px-3 py-2 rounded-lg font-semibold transition">ℹ️ About</a>
      <a href="/contact.php" class="hover:bg-white/20 px-3 py-2 rounded-lg font-semibold transition">📞 Contact</a>
      <a href="/logout.php" class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-2 rounded-lg font-bold transition">🚪 Logout</a>
    </div>
  </div>
</nav>