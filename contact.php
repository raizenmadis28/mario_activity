<?php
require_once __DIR__ . '/includes/layout.php';
render_head('Contact', '/Mario Wallpaper/NSMBUMushroomKingdom.png');
if (is_logged_in()) render_navbar();
?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="max-w-3xl w-full bg-white/95 rounded-3xl shadow-mario border-4 border-blue-600 p-8 my-6">
<div class="h-2 bg-gradient-to-r from-red-500 via-yellow-400 to-blue-500 rounded-full mb-6"></div>
<h2 class="text-3xl font-bold text-blue-700 mb-4 drop-shadow">Makipag-ugnayan</h2>
<div class="text-center my-6">
<img src="/GIFs/Super_Mario_Bros_Plumbing_Commercial.gif" alt="Super Mario Bros Plumbing Commercial" class="max-w-full h-auto border-4 border-yellow-400 rounded-xl shadow-lg mx-auto" onerror="this.style.display='none'">
</div>
<div class="text-gray-800 text-lg leading-relaxed space-y-3">
<p>Para sa mga katanungan o suporta sa sistema, maaari kang mag-abot sa mga sumusunod:</p>
<p><b>Email:</b> <a href="mailto:reneemadis28@gmail.com" class="text-blue-600 font-bold underline hover:text-red-600 transition">reneemadis28@gmail.com</a> at <a href="mailto:lealavieannrivera@gmail.com" class="text-blue-600 font-bold underline hover:text-red-600 transition">lealavieannrivera@gmail.com</a></p>
<p><b>Lokasyon:</b> National University Baliwag</p>
</div>
<?php if (!is_logged_in()): ?>
<div class="text-center mt-6"><a href="/users/login.php" class="inline-block bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-bold border-2 border-black transition-transform hover:scale-105">Pumasok sa System</a></div>
<?php endif; ?>
</div></div>
<?php render_footer(); ?>