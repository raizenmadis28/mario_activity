<?php require __DIR__ . '/../layouts/head.php'; require __DIR__ . '/../layouts/navbar.php'; ?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 max-w-md w-full p-8 text-center text-white my-6">
<img src="/Funny Memes/bowser jr.png" class="w-24 h-24 mx-auto rounded-full border-4 border-red-500 object-cover mb-4" onerror="this.style.display='none'">
<h1 class="text-2xl font-bold text-red-400 mb-2">⚠️ Logout Phase ⚠️</h1>
<p class="text-yellow-300 mb-6">"Gwehehehhehe! Gusto mo na bang lumabas?"</p>
<div class="grid grid-cols-2 gap-3">
<a href="/index.php" class="bg-green-700/60 border-2 border-green-400 rounded-xl p-3 hover:bg-green-600/80">
<img src="/Tools and Guide/Green pipe.jpg" class="w-16 h-auto mx-auto rounded border-2 border-white mb-2" onerror="this.style.display='none'">
<div class="bg-green-700 text-white px-2 py-1 rounded text-xs font-bold">Pumasok sa Pipe</div></a>
<a href="logout.php?confirm=1" class="bg-red-900/60 border-2 border-red-400 rounded-xl p-3 hover:bg-red-700/80">
<img src="/Tools and Guide/Red Lava.png" class="w-16 h-12 object-cover mx-auto rounded border-2 border-white mb-2" onerror="this.style.display='none'">
<div class="bg-red-700 text-white px-2 py-1 rounded text-xs font-bold">Abort (Logout)</div></a>
</div></div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>