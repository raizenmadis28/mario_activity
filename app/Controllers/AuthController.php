<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\CipherMail;
use App\Core\ImageUpload;
use App\Models\User;

class AuthController extends Controller
{
    // ============ LOGIN ============
    public function login(): void
    {
        Auth::requireGuest('/index.php');
        $error = '';
        $error_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = trim($_POST['username'] ?? '');
            $p = $_POST['password'] ?? '';

            if ($u === '' || $p === '') {
                $error = 'Punan lahat ng fields!';
                $error_type = 'empty';
            } else {
                $user = (new User())->findByLogin($u);
                if ($user && password_verify($p, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']  = (int)$user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email']    = $user['email'];
                    $_SESSION['role']     = $user['role'];
                    unset($_SESSION['redirect_after_login']);
                    toast('YEHEYY!! Welcome back, ' . $user['username'] . '!', 'success');
                    $this->redirect('/index.php');
                }
                $error = 'Gwahahaha! Maling password o username!';
                $error_type = 'wrong';
            }
        }

        $this->view('auth/login', [
            'title'      => 'Sign In',
            'bg'         => '/Mario Wallpaper/mario-super-mario-bros-3-wallpaper-preview.jpg',
            'error'      => $error,
            'error_type' => $error_type,
        ]);
    }

    // ============ REGISTER — puro CUSTOMER, LAHAT required ============
    public function register(): void
    {
        Auth::requireGuest('/index.php');
        $error = '';
        $error_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u      = trim($_POST['username'] ?? '');
            $em     = trim($_POST['email'] ?? '');
            $p      = $_POST['password'] ?? '';
            $fullNm = trim($_POST['full_name'] ?? '');
            $addr   = trim($_POST['address'] ?? '');
            $role   = 'Customer';

            $hasImage = isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK;

            if ($u === '' || $em === '' || $p === '' || $fullNm === '' || $addr === '' || !$hasImage) {
                $error = 'OHHH!! MAAMMAAMIAA!! Punan LAHAT ng fields kasama ang profile picture!';
                $error_type = 'gameover';
            } elseif (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
                $error = 'Hindi valid ang email format.';
                $error_type = 'gameover';
            } elseif (strlen($p) < 6) {
                $error = 'Dapat 6+ characters ang password.';
                $error_type = 'gameover';
            } else {
                $img = ImageUpload::handle($_FILES['profile_image'], 'customers');

                if ($img === '') {
                    $error = 'Invalid image. JPG/PNG/WEBP lang, max 5MB.';
                    $error_type = 'gameover';
                } else {
                    $model = new User();
                    if ($model->exists($u, $em)) {
                        ImageUpload::delete($img);
                        $error = 'GWAHAHAHA!! MERON NA USERNAME/EMAIL NA GINAMIT!';
                        $error_type = 'bowser';
                    } else {
                        $id = $model->create([
                            'username'      => $u,
                            'email'         => $em,
                            'password'      => password_hash($p, PASSWORD_DEFAULT),
                            'role'          => $role,
                            'full_name'     => $fullNm,
                            'address'       => $addr,
                            'profile_image' => $img,
                        ]);
                        if ($id) {
                            session_regenerate_id(true);
                            $_SESSION['user_id']  = $id;
                            $_SESSION['username'] = $u;
                            $_SESSION['email']    = $em;
                            $_SESSION['role']     = $role;
                            toast('YEHEYY!! Registered na bilang Customer, ' . $u . '!', 'success');
                            $this->redirect('/index.php');
                        }
                        ImageUpload::delete($img);
                        $error = 'Registration failed. Subukan muli.';
                        $error_type = 'gameover';
                    }
                }
            }
        }

        $this->view('auth/register', [
            'title'      => 'Register',
            'bg'         => '/Mario Wallpaper/Mario-plays.png',
            'error'      => $error,
            'error_type' => $error_type,
        ]);
    }

    // ============ FORGOT PASSWORD ============
    public function forgot(): void
    {
        Auth::requireGuest('/index.php');
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if ($email === '') {
                $error = 'Ilagay ang email address.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Hindi valid ang email format.';
            } else {
                $user = (new User())->findByEmail($email);
                $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                $_SESSION['reset_email']       = $email;
                $_SESSION['reset_username']    = $user['username'] ?? '';
                $_SESSION['verification_code'] = $code;
                $_SESSION['code_expires_at']   = time() + 120;
                $_SESSION['otp_attempts']      = 0;
                $_SESSION['user_exists']       = ($user !== null);

                if ($user) {
                    $r = CipherMail::sendOtp($email, $user['username'], $code);
                    $_SESSION['api_status'] = $r['ok'] ? 'sent' : 'failed';
                    $_SESSION['api_error']  = $r['ok'] ? '' : ($r['error'] ?? '');
                } else {
                    $_SESSION['api_status'] = 'sent';
                    $_SESSION['api_error']  = '';
                }

                toast('📧 Kung registered ang email, may OTP code doon. Tignan ang Inbox, Spam, at Junk folder.', 'info');
                $this->redirect('/users/verifications.php');
            }
        }

        $this->view('auth/forgot', [
            'title' => 'Forgot Password',
            'bg'    => '/Mario Wallpaper/mario-world-map.jpg',
            'error' => $error,
        ]);
    }

    // ============ VERIFY OTP ============
    public function verify(): void
    {
        Auth::requireGuest('/index.php');

        if (!isset($_SESSION['verification_code']) || !isset($_SESSION['reset_email'])) {
            $this->redirect('/users/forgot_password.php');
        }

        $message = '';
        $message_type = '';

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
                if (!empty($_SESSION['user_exists'])) {
                    CipherMail::sendOtp($_SESSION['reset_email'], $_SESSION['reset_username'] ?? '', $newCode);
                }
                toast('📧 Bagong OTP code naipadala. Tignan ang Inbox, Spam, at Junk folder.', 'info');
                $this->redirect('/users/verifications.php');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'verify') {
            $input = trim($_POST['verification_code'] ?? '');
            $expiresAt = (int)($_SESSION['code_expires_at'] ?? 0);
            $now = time();

            if ($now >= $expiresAt) {
                $message = 'Expired na ang code. I-click ang Resend.';
                $message_type = 'expire';
            } elseif ($input === '') {
                $message = 'Ilagay ang 6-digit code.';
                $message_type = 'error';
            } elseif (!hash_equals((string)$_SESSION['verification_code'], $input)) {
                $_SESSION['otp_attempts'] = ((int)($_SESSION['otp_attempts'] ?? 0)) + 1;
                if ($_SESSION['otp_attempts'] >= 5) {
                    unset($_SESSION['verification_code'], $_SESSION['code_expires_at']);
                    $message = 'Sobra na sa 5 attempts. Bumalik sa Forgot Password.';
                    $message_type = 'expire';
                } else {
                    $left = 5 - $_SESSION['otp_attempts'];
                    $message = "Maling code. Attempts left: {$left}";
                    $message_type = 'error';
                }
            } elseif (empty($_SESSION['user_exists'])) {
                $_SESSION['otp_attempts']++;
                $message = 'Maling code. Subukan muli.';
                $message_type = 'error';
            } else {
                $_SESSION['verified_success'] = true;
                toast('✅ Verified! Mag-set na ng bagong password.', 'success');
                $this->redirect('/users/reset_password.php');
            }
        }

        $remaining = max(0, (int)($_SESSION['code_expires_at'] ?? 0) - time());
        $this->view('auth/verify', [
            'title'           => 'Verification Code',
            'bg'              => '/Mario Wallpaper/Mario 3D Land.jpeg',
            'message'         => $message,
            'message_type'    => $message_type,
            'remaining'       => $remaining,
            'api_status'      => $_SESSION['api_status'] ?? '',
            'api_error'       => $_SESSION['api_error'] ?? '',
            'show_debug_code' => (($_SESSION['api_status'] ?? '') === 'failed') || isset($_GET['debug']),
        ]);
    }

    // ============ RESET PASSWORD ============
    public function reset(): void
    {
        Auth::requireGuest('/index.php');
        if (!isset($_SESSION['reset_email']) || empty($_SESSION['verified_success']) || empty($_SESSION['user_exists'])) {
            $this->redirect('/users/forgot_password.php');
        }

        $message = '';
        $message_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new  = $_POST['new_password'] ?? '';
            $conf = $_POST['confirm_password'] ?? '';

            if (empty($new) || empty($conf)) {
                $message = 'Punan ang password fields!';
                $message_type = 'error';
            } elseif ($new !== $conf) {
                $message = 'Hindi nagtutugma ang mga password!';
                $message_type = 'error';
            } else {
                $len = strlen($new);
                $num = preg_match('/[0-9]/', $new);
                $chr = preg_match('/[a-zA-Z]/', $new);
                $sym = preg_match('/[\W_]/', $new);

                if ($len < 6)               { $message = 'VERY WEAK (min 6)'; $message_type = 'error'; }
                elseif ($len < 8)            { $message = 'WEAK (need 8+)'; $message_type = 'error'; }
                elseif (!$num||!$chr||!$sym) { $message = 'TOO WEAK! Letters + numbers + symbols.'; $message_type = 'error'; }
                else {
                    $ok = (new User())->updatePasswordByEmail(
                        $_SESSION['reset_email'],
                        password_hash($new, PASSWORD_DEFAULT)
                    );
                    unset(
                        $_SESSION['reset_email'], $_SESSION['reset_username'],
                        $_SESSION['verification_code'], $_SESSION['code_expires_at'],
                        $_SESSION['verified_success'], $_SESSION['api_status'],
                        $_SESSION['api_error'], $_SESSION['otp_attempts'],
                        $_SESSION['user_exists']
                    );
                    if ($ok) {
                        toast('YEHEYY!! Nareset na ang password. Mag-login gamit ang bago.', 'success');
                        $this->redirect('/users/login.php');
                    }
                    $message = 'Reset failed. Subukan muli.';
                    $message_type = 'error';
                }
            }
        }

        $this->view('auth/reset', [
            'title'        => 'Reset Password',
            'bg'           => '/Mario Wallpaper/SuperStar_Road.png',
            'message'      => $message,
            'message_type' => $message_type,
        ]);
    }

    // ============ EDIT USER (with password change + avatar) ============
    public function edit(): void
    {
        Auth::requireLogin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $current = Auth::user();
        $isAdmin = Auth::isAdmin();

        // Guard: customer cannot edit other users
        if (!$isAdmin && $id !== (int)$current['id']) {
            $this->redirect('/index.php');
        }

        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            toast('User not found!', 'error');
            $this->redirect('/index.php');
        }

        $message = '';
        $message_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u      = trim($_POST['username'] ?? '');
            $em     = trim($_POST['email'] ?? '');
            $r      = $isAdmin ? ($_POST['role'] ?? $user['role']) : $current['role'];
            $fullNm = trim($_POST['full_name'] ?? '');
            $addr   = trim($_POST['address'] ?? '');
            $img    = $user['profile_image'] ?? '';

            // Password change fields
            $newPw      = $_POST['new_password'] ?? '';
            $confPw     = $_POST['confirm_password'] ?? '';
            $changePass = ($newPw !== '' || $confPw !== '');

            // Optional new avatar
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $newImg = ImageUpload::handle($_FILES['profile_image'], 'customers');
                if ($newImg !== '') {
                    if ($img !== '') ImageUpload::delete($img);
                    $img = $newImg;
                }
            }

            // ===== VALIDATION =====
            if ($u === '' || $em === '') {
                $message = 'Punan ang username at email!';
                $message_type = 'error';
            } elseif (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
                $message = 'Hindi valid ang email format.';
                $message_type = 'error';
            } elseif ($model->existsExcept($u, $em, $id)) {
                $message = 'Ginamit na ang username o email ng ibang user.';
                $message_type = 'error';
            } elseif ($changePass && $newPw !== $confPw) {
                $message = 'Hindi nagtutugma ang bagong password at confirm password!';
                $message_type = 'error';
            } elseif ($changePass && strlen($newPw) < 6) {
                $message = 'Dapat 6+ characters ang bagong password.';
                $message_type = 'error';
            } else {
                // ===== UPDATE PROFILE =====
                $ok = $model->update($id, [
                    'username'      => $u,
                    'email'         => $em,
                    'role'          => $r,
                    'full_name'     => $fullNm,
                    'address'       => $addr,
                    'profile_image' => $img,
                ]);

                // ===== UPDATE PASSWORD (kung may binago) =====
                $passOk = true;
                if ($ok && $changePass) {
                    $passOk = $model->updatePasswordById(
                        $id,
                        password_hash($newPw, PASSWORD_DEFAULT)
                    );
                }

                if ($ok && $passOk) {
                    if ($id === (int)$current['id']) {
                        $_SESSION['username'] = $u;
                        $_SESSION['email']    = $em;
                        $_SESSION['role']     = $r;
                    }
                    $msg = $changePass
                        ? 'YEHEYY!! Profile + password updated!'
                        : 'YEHEYY!! Profile updated!';
                    toast($msg, 'success');
                    $this->redirect('/users/edit_user.php?id=' . $id);
                }
                $message = $passOk ? 'Update failed!' : 'Password update failed!';
                $message_type = 'error';
            }
            $user = $model->find($id);
        }

        $this->view('auth/edit', [
            'title'        => 'Edit User',
            'bg'           => '/Mario Wallpaper/mario-super-mario-bros-3-wallpaper-preview.jpg',
            'user'         => $user,
            'is_admin'     => $isAdmin,
            'message'      => $message,
            'message_type' => $message_type,
        ]);
    }

    // ============ LOGOUT ============
    public function logout(): void
    {
        if (isset($_GET['confirm'])) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();
            session_start();
            toast('Naka-logout ka na. Bye!', 'info');
            $this->redirect('/users/login.php');
        }
        $this->redirect('/index.php');
    }
}