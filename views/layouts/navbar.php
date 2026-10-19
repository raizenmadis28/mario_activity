<?php
if (!\App\Core\Auth::isLoggedIn()) return;
$u = \App\Core\Auth::user();

// Fetch profile image mula sa DB (1 query per page, fine)
$navUser = (new \App\Models\User())->find((int)$u['id']);
$profileImg = $navUser['profile_image'] ?? '';
$hasAvatar = !empty($profileImg) && file_exists(__DIR__ . '/../../' . $profileImg);
$initial   = strtoupper(substr($u['username'] ?? '?', 0, 1));

$isAdmin   = \App\Core\Auth::isAdmin();
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<nav class="bg-gradient-to-r from-red-600 to-red-500 text-white shadow-lg border-b-4 border-yellow-400 flex-shrink-0 sticky top-0 z-30">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-3">

    <a href="/index.php" class="text-xl sm:text-2xl font-bold hover:scale-105 transition-transform whitespace-nowrap">
      🍄 RaizenMadis28
    </a>

    <!-- DESKTOP NAV -->
    <div class="hidden md:flex items-center gap-2 text-sm">

      <!-- User pill WITH avatar -->
      <a href="/users/edit_user.php?id=<?= (int)$u['id'] ?>"
         class="bg-white/20 hover:bg-white/30 transition pl-1 pr-3 py-1 rounded-full flex items-center gap-2"
         title="Edit profile">
        <?php if ($hasAvatar): ?>
          <img src="/<?= e($profileImg) ?>" alt="Avatar"
               class="w-8 h-8 rounded-full object-cover border-2 border-white/60">
        <?php else: ?>
          <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center text-sm font-bold text-black border-2 border-white/60">
            <?= e($initial) ?>
          </div>
        <?php endif; ?>
        <span>
          <?= e($u['username']) ?>
          <span class="opacity-75">(<?= e($u['role']) ?>)</span>
        </span>
      </a>

      <a href="/index.php"   class="hover:bg-white/20 px-3 py-2 rounded-lg font-semibold transition">🏠 Home</a>
      <a href="/about.php"   class="hover:bg-white/20 px-3 py-2 rounded-lg font-semibold transition">ℹ️ About</a>
      <a href="/contact.php" class="hover:bg-white/20 px-3 py-2 rounded-lg font-semibold transition">📞 Contact</a>

      <?php if (!$isAdmin): ?>
        <a href="/cart.php" class="relative hover:bg-white/20 px-3 py-2 rounded-lg font-semibold transition">
          🛒 Cart
          <?php if ($cartCount > 0): ?>
            <span class="absolute -top-1 -right-1 bg-yellow-500 text-black text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
              <?= (int)$cartCount ?>
            </span>
          <?php endif; ?>
        </a>
      <?php endif; ?>

      <button type="button" onclick="openLogoutModal()"
              class="bg-yellow-500 hover:bg-yellow-600 active:scale-95 text-black px-3 py-2 rounded-lg font-bold transition">
        🚪 Logout
      </button>
    </div>

    <!-- MOBILE HAMBURGER (may avatar preview) -->
    <button onclick="openMobileMenu()"
            class="md:hidden bg-white/20 hover:bg-white/30 active:scale-95 p-1.5 rounded-lg transition relative flex items-center gap-2"
            aria-label="Open menu">
      <?php if ($hasAvatar): ?>
        <img src="/<?= e($profileImg) ?>" alt="Avatar"
             class="w-7 h-7 rounded-full object-cover border-2 border-white/60">
      <?php else: ?>
        <div class="w-7 h-7 rounded-full bg-yellow-500 flex items-center justify-center text-xs font-bold text-black border-2 border-white/60">
          <?= e($initial) ?>
        </div>
      <?php endif; ?>

      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="3" y1="6"  x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>

      <?php if (!$isAdmin && $cartCount > 0): ?>
        <span class="absolute -top-1 -right-1 bg-yellow-500 text-black text-[10px] rounded-full w-5 h-5 flex items-center justify-center font-bold">
          <?= (int)$cartCount ?>
        </span>
      <?php endif; ?>
    </button>
  </div>
</nav>

<!-- MOBILE SLIDING SIDEBAR -->
<div id="mobile-menu" class="fixed inset-0 z-40 hidden md:hidden" aria-hidden="true">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeMobileMenu()"></div>

  <aside id="mobile-panel"
         class="absolute top-0 right-0 h-full w-80 max-w-[85vw] bg-gradient-to-b from-red-600 to-red-500 text-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col"
         role="dialog" aria-modal="true">

    <div class="flex items-center justify-between p-4 border-b-2 border-yellow-400 flex-shrink-0">
      <span class="text-xl font-bold">🍄 Menu</span>
      <button onclick="closeMobileMenu()"
              class="bg-white/20 hover:bg-white/30 active:scale-95 p-2 rounded-lg transition"
              aria-label="Close menu">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="18" y1="6" x2="6"  y2="18"></line>
          <line x1="6"  y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- USER CARD WITH AVATAR -->
    <a href="/users/edit_user.php?id=<?= (int)$u['id'] ?>"
       class="m-3 p-4 bg-white/10 hover:bg-white/20 transition border-2 border-white/20 rounded-xl text-center flex-shrink-0 block">
      <?php if ($hasAvatar): ?>
        <img src="/<?= e($profileImg) ?>" alt="Avatar"
             class="w-20 h-20 mx-auto rounded-full object-cover border-4 border-white/60 mb-2 shadow-lg">
      <?php else: ?>
        <div class="w-20 h-20 mx-auto rounded-full bg-yellow-500 flex items-center justify-center text-3xl font-bold text-black border-4 border-white/60 mb-2 shadow-lg">
          <?= e($initial) ?>
        </div>
      <?php endif; ?>
      <div class="font-bold text-base text-white"><?= e($u['username']) ?></div>
      <div class="text-xs text-white/75 mt-0.5">Role: <?= e($u['role']) ?></div>
      <div class="text-[10px] text-yellow-300 mt-1 font-semibold">Tap para i-edit ang profile</div>
    </a>

    <nav class="flex-1 p-3 space-y-2 overflow-y-auto">
      <a href="/index.php"   class="block hover:bg-white/20 active:bg-white/30 px-4 py-3 rounded-xl font-semibold transition text-white">🏠 Home</a>
      <a href="/about.php"   class="block hover:bg-white/20 active:bg-white/30 px-4 py-3 rounded-xl font-semibold transition text-white">ℹ️ About</a>
      <a href="/contact.php" class="block hover:bg-white/20 active:bg-white/30 px-4 py-3 rounded-xl font-semibold transition text-white">📞 Contact</a>

      <?php if (!$isAdmin): ?>
        <a href="/cart.php" class="flex items-center justify-between hover:bg-white/20 active:bg-white/30 px-4 py-3 rounded-xl font-semibold transition text-white">
          <span>🛒 Cart</span>
          <?php if ($cartCount > 0): ?>
            <span class="bg-yellow-500 text-black text-xs rounded-full px-2 py-0.5 font-bold">
              <?= (int)$cartCount ?>
            </span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
    </nav>

    <div class="p-3 border-t-2 border-yellow-400 flex-shrink-0">
      <button type="button" onclick="openLogoutModalFromMobile()"
              class="block w-full bg-yellow-500 hover:bg-yellow-600 active:scale-95 text-black px-4 py-3 rounded-xl font-bold text-center transition">
        🚪 Logout
      </button>
    </div>
  </aside>
</div>

<!-- LOGOUT CONFIRMATION MODAL -->
<div id="logout-modal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[60] items-center justify-center p-4">
  <div class="bg-slate-800 rounded-2xl shadow-2xl border-4 border-yellow-400 max-w-md w-full p-6 text-center text-white animate-[fadeIn_.2s_ease-out]">

    <img src="/Funny Memes/bowser jr.png"
         class="w-24 h-24 mx-auto rounded-full border-4 border-red-500 object-cover mb-4"
         onerror="this.style.display='none'">

    <h2 class="text-2xl font-bold text-red-400 mb-2">⚠️ Logout</h2>
    <p class="text-yellow-300 mb-6">"Gwehehehhehe! Gusto mo na bang lumabas?"</p>

    <div class="grid grid-cols-2 gap-3">
      <button type="button" onclick="closeLogoutModal()"
              class="bg-green-700/60 border-2 border-green-400 rounded-xl p-3 hover:bg-green-600/80 active:scale-95 transition flex flex-col items-center">
        <img src="/Tools and Guide/Green pipe.jpg"
             class="w-14 h-auto rounded border-2 border-white mb-2" onerror="this.style.display='none'">
        <span class="text-xs font-bold">Manatili</span>
      </button>

      <a href="/logout.php?confirm=1"
         class="bg-red-900/60 border-2 border-red-400 rounded-xl p-3 hover:bg-red-700/80 active:scale-95 transition flex flex-col items-center">
        <img src="/Tools and Guide/Red Lava.png"
             class="w-14 h-10 object-cover rounded border-2 border-white mb-2" onerror="this.style.display='none'">
        <span class="text-xs font-bold">Logout</span>
      </a>
    </div>
  </div>
</div>

<style>
@keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>

<script>
(function() {
  const mobileMenu   = document.getElementById('mobile-menu');
  const mobilePanel  = document.getElementById('mobile-panel');
  const logoutModal  = document.getElementById('logout-modal');
  const body         = document.body;

  window.openMobileMenu = function() {
    mobileMenu.classList.remove('hidden');
    mobileMenu.setAttribute('aria-hidden', 'false');
    body.style.overflow = 'hidden';
    requestAnimationFrame(() => mobilePanel.classList.remove('translate-x-full'));
  };

  window.closeMobileMenu = function() {
    mobilePanel.classList.add('translate-x-full');
    body.style.overflow = '';
    setTimeout(() => {
      mobileMenu.classList.add('hidden');
      mobileMenu.setAttribute('aria-hidden', 'true');
    }, 300);
  };

  window.openLogoutModal = function() {
    logoutModal.classList.remove('hidden');
    logoutModal.classList.add('flex');
    body.style.overflow = 'hidden';
  };

  window.closeLogoutModal = function() {
    logoutModal.classList.add('hidden');
    logoutModal.classList.remove('flex');
    body.style.overflow = '';
  };

  window.openLogoutModalFromMobile = function() {
    mobilePanel.classList.add('translate-x-full');
    setTimeout(() => {
      mobileMenu.classList.add('hidden');
      mobileMenu.setAttribute('aria-hidden', 'true');
      openLogoutModal();
    }, 300);
  };

  logoutModal.addEventListener('click', (e) => {
    if (e.target === logoutModal) closeLogoutModal();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    if (!logoutModal.classList.contains('hidden')) closeLogoutModal();
    else if (!mobileMenu.classList.contains('hidden')) closeMobileMenu();
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768 && !mobileMenu.classList.contains('hidden')) closeMobileMenu();
  });
})();
</script>