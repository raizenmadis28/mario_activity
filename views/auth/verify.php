<?php require __DIR__ . '/../layouts/head.php'; ?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-lg bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-8 text-white my-6">
<h2 class="text-2xl font-bold text-yellow-300 text-center mb-1">🔐 Verification</h2>
<p class="text-center text-gray-300 text-sm mb-2">Naipadala sa <strong class="text-yellow-200"><?= e($_SESSION['reset_email']) ?></strong></p>
<p class="text-center text-xs text-gray-400 mb-4">💡 Tignan din ang <strong class="text-yellow-300">Spam / Junk folder</strong></p>
<?php if ($api_status === 'failed'): ?>
<div class="bg-red-700/40 border-2 border-red-400 text-red-100 px-4 py-2 rounded-lg text-xs mb-4">
⚠️ <strong>Email failed:</strong> <?= e($api_error) ?>
<?php if ($show_debug_code): ?>
<br><span class="text-yellow-300">🔧 <strong>Debug:</strong> <span class="font-mono text-lg"><?= e($_SESSION['verification_code']) ?></span></span>
<?php endif; ?>
</div>
<?php endif; ?>
<div class="text-center mb-5">
<div class="text-xs text-yellow-300 font-bold uppercase mb-1">⏳ Time remaining</div>
<div id="timer" class="text-5xl font-mono font-bold <?= $remaining <= 0 ? 'text-gray-500' : 'text-red-400' ?>">
<?= sprintf('%02d:%02d', intdiv($remaining, 60), $remaining % 60) ?>
</div>
</div>
<?php if ($message): ?>
<div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold mb-4 text-center text-sm"><?= e($message) ?></div>
<?php endif; ?>
<form method="POST" id="verify-form" class="space-y-3" <?= $remaining <= 0 ? 'style="display:none"' : '' ?>>
<input type="hidden" name="action" value="verify">
<div>
<label class="block font-bold text-yellow-300 mb-1 text-sm text-center">6-digit code</label>
<input type="text" name="verification_code" maxlength="6" pattern="\d{6}" inputmode="numeric"
    placeholder="••••••" autocomplete="one-time-code" required
    class="w-full px-3 py-3 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg text-center text-3xl tracking-[0.5em] font-mono">
</div>
<button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold border-2 border-yellow-400">
✅ Verify Code
</button>
</form>
<form method="POST" id="resend-form" class="space-y-3" <?= $remaining <= 0 ? '' : 'style="display:none"' ?>>
<input type="hidden" name="action" value="resend">
<div class="bg-red-900/40 border-2 border-red-400 px-4 py-3 rounded-lg text-sm text-center text-red-100">
⌛ Expired na ang code. I-request ang bago.
</div>
<button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-black py-3 rounded-xl font-bold border-2 border-black">
📧 Resend Code
</button>
</form>
<div class="text-center text-sm pt-4 border-t border-slate-600 mt-5">
<a href="forgot_password.php" class="text-yellow-300 font-bold hover:underline">⬅️ Bumalik</a>
</div>
</div></div>
<script>
const START_REMAINING = <?= (int)$remaining ?>;
const START_TIME = Date.now();
const timerEl = document.getElementById('timer');
const verifyForm = document.getElementById('verify-form');
const resendForm = document.getElementById('resend-form');
let expiredShown = false;
function fmt(sec){const m=Math.floor(sec/60),s=sec%60;return String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');}
function tick(){
  const rem=Math.max(0,START_REMAINING-Math.floor((Date.now()-START_TIME)/1000));
  timerEl.textContent=fmt(rem);
  if(rem<=0){if(!expiredShown){expiredShown=true;verifyForm.style.display='none';resendForm.style.display='block';timerEl.classList.remove('text-red-400');timerEl.classList.add('text-gray-500');}return;}
  setTimeout(tick,250);
}
if(START_REMAINING>0)setTimeout(tick,250);else{verifyForm.style.display='none';resendForm.style.display='block';}
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>