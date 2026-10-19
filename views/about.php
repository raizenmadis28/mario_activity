<?php
require __DIR__ . '/layouts/head.php';
require __DIR__ . '/layouts/navbar.php';
$loggedIn = \App\Core\Auth::isLoggedIn();
?>
<div class="flex-1 flex items-center justify-center p-4">
  <div class="max-w-3xl w-full bg-white/90 rounded-3xl shadow-mario border-4 border-blue-600 p-8 my-6">
    <div class="h-2 bg-gradient-to-r from-red-500 via-yellow-400 to-blue-500 rounded-full mb-6"></div>
    <h2 class="text-3xl font-bold text-blue-700 mb-4">Tungkol sa Sistema</h2>
    <div class="text-center my-6">
      <img src="/GIFs/mario-bros.gif" alt="Mario Bros Animation"
           class="max-w-full h-auto border-4 border-yellow-400 rounded-xl shadow-lg mx-auto"
           onerror="this.style.display='none'">
    </div>
    <p class="text-gray-800 text-lg leading-relaxed">
      Ang sistemang ito ay isang <em>Mario-themed Inventory and Management System</em> na
      naglalaman ng mga modyul para sa Admin, Customer, at Inventory. Mayroon din itong
      automated file-based image handling para sa maayos na pag-upload at pagbura ng mga larawan.
    </p>
    <?php if (!$loggedIn): ?>
      <div class="text-center mt-6">
        <a href="/users/login.php"
           class="inline-block bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-bold border-2 border-black">
          Pumasok sa System
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/layouts/footer.php'; ?>