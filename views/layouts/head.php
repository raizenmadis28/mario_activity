<?php
// views/layouts/head.php
// Expects: $title (string), $bg (string, optional)

$title = $title ?? 'RaizenMadis28';
$bg    = $bg    ?? '/Mario Wallpaper/NSMBUMushroomKingdom.png';
?>
<!DOCTYPE html>
<html lang="tl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> - RaizenMadis28</title>

<!-- 🍄 Favicon -->
<link rel="icon" type="image/x-icon" href="/Mario Wallpaper/toad.ico">
<link rel="shortcut icon" type="image/x-icon" href="/Mario Wallpaper/toad.ico">
<link rel="apple-touch-icon" href="/Mario Wallpaper/toad.ico">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
<style>
html{min-height:100%}
body{min-height:100vh;font-family:'Fredoka',system-ui,sans-serif;background-image:url('<?= e($bg) ?>');background-size:cover;background-attachment:fixed;background-position:center;background-repeat:no-repeat}
@keyframes slideIn{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}
.toast-anim{animation:slideIn .3s ease-out}
.shadow-mario{box-shadow:6px 6px 0 rgba(0,0,0,.85)}
*:focus-visible{outline:3px solid #fbbf24;outline-offset:2px}
</style>
<script>
function showToast(msg,type='success'){
  const c={success:'bg-green-500',error:'bg-red-500',warning:'bg-yellow-500',info:'bg-blue-500'};
  const el=document.createElement('div');
  el.className=`toast-anim pointer-events-auto ${c[type]||c.info} text-white px-5 py-3 rounded-xl shadow-lg max-w-md font-semibold`;
  el.textContent=msg;
  document.getElementById('toast-container').appendChild(el);
  setTimeout(()=>{el.style.opacity='0';el.style.transition='opacity .3s';setTimeout(()=>el.remove(),300)},3500);
}
function confirmDialog(message,cb){
  const m=document.getElementById('confirm-modal');
  document.getElementById('confirm-msg').textContent=message;
  m.classList.remove('hidden');m.classList.add('flex');
  document.getElementById('confirm-ok').onclick=()=>{m.classList.add('hidden');m.classList.remove('flex');cb();};
  document.getElementById('confirm-cancel').onclick=()=>{m.classList.add('hidden');m.classList.remove('flex');};
}
function confirmLink(url,message){confirmDialog(message||'Sigurado ka ba?',()=>{window.location.href=url;});}
</script>
</head>
<body class="flex flex-col min-h-screen">
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>
<div id="confirm-modal" class="hidden fixed inset-0 bg-black/60 z-40 items-center justify-center p-4 backdrop-blur-sm">
  <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 border-4 border-yellow-400">
    <h3 class="text-xl font-bold text-red-600 mb-2">⚠️ Confirmation</h3>
    <p id="confirm-msg" class="text-gray-700 mb-6"></p>
    <div class="flex justify-end gap-3">
      <button id="confirm-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg font-bold transition">Cancel</button>
      <button id="confirm-ok" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition">OK</button>
    </div>
  </div>
</div>