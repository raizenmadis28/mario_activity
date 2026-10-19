<?php
require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/ciphermail.php';
require_guest('/index.php');

if (!isset($_SESSION['verification_code']) || !isset($_SESSION['reset_email'])) {
    redirect('/users/forgot_password.php');
}

$message = '';
$message_type = '';

// ============ RESEND HANDLER ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'resend') {
    $remaining = max(0, (int)($_SESSION['code_expires_at'] ?? 0) - time());

    if ($remaining > 0) {
        $message = "Hintayin pa ang {$remaining}s bago mag-resend.";
        $message_type = 'error';
    } else {
        $newCode = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['verification_code'] = $newCode;
        $_SESSION['code_expires_at']   = time() + 120;
        $_SESSION['otp_attempts']      = 0;

        // Send lang kung existing user — silent kung wala
        if (!empty($_SESSION['user_exists'])) {
            send_otp_email($_SESSION['reset_email'], $_SESSION['reset_username'] ?? '', $newCode);
        }

        // Palaging parehong toast
        toast('📧 Bagong OTP code naipadala. Tignan ang Inbox, Spam, at Junk folder.', 'info');
        redirect('/users/verifications.php');
    }
}

// ============ VERIFY HANDLER ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'verify') {
    $input = trim($_POST['verification_code'] ?? '');
    $expiresAt = (int)($_SESSION['code_expires_at'] ?? 0);
    $now = time();

    if ($now >= $expiresAt) {
        $message = 'Expired na ang code. I-click ang Resend para makakuha ng bago.';
        $message_type = 'expire';
    } elseif ($input === '') {
        $message = 'Ilagay ang 6-digit code.';
        $message_type = 'error';
    } elseif (!hash_equals((string)$_SESSION['verification_code'], $input)) {
        // Maling code
        $_SESSION['otp_attempts'] = ((int)($_SESSION['otp_attempts'] ?? 0)) + 1;
        if ($_SESSION['otp_attempts'] >= 5) {
            unset($_SESSION['verification_code'], $_SESSION['code_expires_at']);
            $message = 'Sobra na sa 5 attempts. Bumalik sa Forgot Password at mag-request muli.';
            $message_type = 'expire';
        } else {
            $left = 5 - $_SESSION['otp_attempts'];
            $message = "Maling code. Natitira pang attempts: {$left}";
            $message_type = 'error';
        }
    } elseif (empty($_SESSION['user_exists'])) {
        // Tama ang code PERO wala ang email sa DB.
        // Impossible ma-guess ito ng attacker (1/1M), at kahit tama, hindi natin ipapasa.
        $_SESSION['otp_attempts']++;
        $message = 'Maling code. Subukan muli.';
        $message_type = 'error';
    } else {
        // ✅ SUCCESS — tama ang code at existing ang user
        $_SESSION['verified_success'] = true;
        toast('✅ Verified! Mag-set na ng bagong password.', 'success');
        redirect('/users/reset_password.php');
    }
}

// Recompute timer — persists across refresh
$remaining = max(0, (int)($_SESSION['code_expires_at'] ?? 0) - time());
$apiStatus = $_SESSION['api_status'] ?? '';
$apiError  = $_SESSION['api_error'] ?? '';
$showDebugCode = ($apiStatus === 'failed') || isset($_GET['debug']);
$expired = ($remaining <= 0);

render_head('Verification Code', '/Mario Wallpaper/Mario 3D Land.jpeg');
?>
<div class="flex-1 flex items-center justify-center p-4">
<div class="w-full max-w-lg bg-slate-800/95 rounded-2xl shadow-mario border-4 border-yellow-400 p-8 text-white my-6">

<h2 class="text-2xl font-bold text-yellow-300 text-center mb-1">🔐 Verification</h2>
<p class="text-center text-gray-300 text-sm mb-2">Naipadala sa <strong class="text-yellow-200"><?= e($_SESSION['reset_email']) ?></strong></p>
<p class="text-center text-xs text-gray-400 mb-4">💡 Hindi mo makita? Tignan ang <strong class="text-yellow-300">Spam / Junk folder</strong></p>

<?php if ($apiStatus === 'failed'): ?>
<div class="bg-red-700/40 border-2 border-red-400 text-red-100 px-4 py-2 rounded-lg text-xs mb-4">
⚠️ <strong>Email failed:</strong> <?= e($apiError) ?>
<?php if ($showDebugCode): ?>
<br><span class="text-yellow-300">🔧 <strong>Debug code:</strong> <span class="font-mono text-lg"><?= e($_SESSION['verification_code']) ?></span></span>
<?php endif; ?>
</div>
<?php endif; ?>

<!-- TIMER -->
<div class="text-center mb-5">
<div class="text-xs text-yellow-300 font-bold uppercase tracking-wider mb-1">⏳ Time remaining</div>
<div id="timer" class="text-5xl font-mono font-bold <?= $expired ? 'text-gray-500' : 'text-red-400' ?> drop-shadow">
<?= sprintf('%02d:%02d', intdiv($remaining, 60), $remaining % 60) ?>
</div>
</div>

<?php if ($message): ?>
<div class="bg-red-700/90 border-2 border-yellow-400 text-white px-4 py-3 rounded-lg font-semibold mb-4 text-center text-sm">
<?= e($message) ?>
</div>
<?php endif; ?>

<!-- VERIFY FORM -->
<form method="POST" id="verify-form" class="space-y-3" <?= $expired ? 'style="display:none"' : '' ?>>
<input type="hidden" name="action" value="verify">
<div>
<label class="block font-bold text-yellow-300 mb-1 text-sm text-center">Ilagay ang 6-digit code</label>
<input type="text" name="verification_code" maxlength="6" pattern="\d{6}" inputmode="numeric"
    placeholder="••••••" autocomplete="one-time-code" required
    class="w-full px-3 py-3 bg-slate-900 text-white border-2 border-yellow-400 rounded-lg text-center text-3xl tracking-[0.5em] font-mono">
</div>
<button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold border-2 border-yellow-400 transition">
✅ Verify Code
</button>
</form>

<!-- RESEND FORM -->
<form method="POST" id="resend-form" class="space-y-3" <?= $expired ? '' : 'style="display:none"' ?>>
<input type="hidden" name="action" value="resend">
<div class="bg-red-900/40 border-2 border-red-400 px-4 py-3 rounded-lg text-sm text-center text-red-100">
⌛ Expired na ang code. I-request ang bago para sa 2-minutong countdown ulit.
</div>
<button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-black py-3 rounded-xl font-bold border-2 border-black transition">
📧 Resend Code
</button>
</form>

<div class="text-center text-sm pt-4 border-t border-slate-600 mt-5">
<a href="forgot_password.php" class="text-yellow-300 font-bold hover:underline">⬅️ Bumalik sa Forgot Password</a>
</div>
</div></div>

<script>
const START_REMAINING = <?= (int)$remaining ?>;
const START_TIME = Date.now();
const timerEl = document.getElementById('timer');
const verifyForm = document.getElementById('verify-form');
const resendForm = document.getElementById('resend-form');
let expiredShown = false;

function fmt(sec) {
    const m = Math.floor(sec / 60);
    const s = sec % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}

function tick() {
    const elapsed = Math.floor((Date.now() - START_TIME) / 1000);
    const rem = Math.max(0, START_REMAINING - elapsed);
    timerEl.textContent = fmt(rem);
    if (rem <= 0) {
        if (!expiredShown) {
            expiredShown = true;
            verifyForm.style.display = 'none';
            resendForm.style.display = 'block';
            timerEl.classList.remove('text-red-400');
            timerEl.classList.add('text-gray-500');
        }
        return;
    }
    setTimeout(tick, 250);
}

if (START_REMAINING > 0) {
    setTimeout(tick, 250);
} else {
    expiredShown = true;
    verifyForm.style.display = 'none';
    resendForm.style.display = 'block';
}
</script>
<?php render_footer(); ?>