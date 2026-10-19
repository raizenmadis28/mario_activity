<?php
require_once __DIR__ . '/includes/layout.php';
if (isset($_GET['confirm'])) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();
    session_start();
    toast('Naka-logout ka na. Bye!', 'info');
    redirect('/users/login.php');
}
require_login();
render_head('Logout', '/Mario Wallpaper/NSMBUMushroomKingdom.png');
?>
<div class="min-h-screen flex items-center justify-center p-4">
<div class="bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 max-w-md w-full p-8 text-center text-white">
<img src="/Funny Memes/bowser jr.png" alt="Bowser Jr." class="w-24 h-24 mx-auto rounded-full border-4 border-red-500 object-cover mb-4" onerror="this.style.display='none'">
<h1 class="text-2xl font-bold text-red-400 mb-2">⚠️ Logout Phase ⚠️</h1>
<p class="text-yellow-300 mb-6">"Gwehehehhehe! Gusto mo na bang lumabas o magbago ang isip sa kaharian?"</p>
<div class="grid grid-cols-2 gap-3">
<a href="/index.php" class="bg-green-700/60 border-2 border-green-400 rounded-xl p-3 hover:bg-green-600/80 transition">
<img src="/Tools and Guide/Green pipe.jpg" alt="Green Pipe" class="w-16 h-auto mx-auto rounded border-2 border-white mb-2" onerror="this.style.display='none'">
<div class="bg-green-700 text-white px-2 py-1 rounded text-xs font-bold">Pumasok sa Pipe</div>
</a>
<a href="logout.php?confirm=1" class="bg-red-900/60 border-2 border-red-400 rounded-xl p-3 hover:bg-red-700/80 transition">
<img src="/Tools and Guide/Red Lava.png" alt="Red Lava" class="w-16 h-12 object-cover mx-auto rounded border-2 border-white mb-2" onerror="this.style.display='none'">
<div class="bg-red-700 text-white px-2 py-1 rounded text-xs font-bold">Abort (Logout)</div>
</a>
</div></div></div>
<?php render_footer(); ?>