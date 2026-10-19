<?php
// users/sign-in.php - Legacy login page (kept for backward compatibility)
require_once __DIR__ . '/../includes/auth.php';
require_guest('/index.php');
require_once __DIR__ . '/../Model/DB_Model.php';

if (!isset($connection) && isset($conn)) $connection = $conn;

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['login_input']);
    $password = $_POST['password'];

    if (empty($login_input) || empty($password)) {
        $error = "MAMAMIAA!! Punan ang lahat ng fields!";
    } else {
        $stmt = $connection->prepare("SELECT id, username, password, email, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $login_input, $login_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'] ?? '';
                $_SESSION['role']     = $user['role'];
                header("Location: ../index.php");
                exit();
            } else {
                $error = "GAME OVER! Mali ang Password!";
            }
        } else {
            $error = "MAMAMIAA! Walang rehistradong Username o Email!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Sign In - RaizenMadis28</title>
<style>
    body { margin:0; font-family:Arial,sans-serif; background:url('../Mario Wallpaper/mario-super-mario-bros-3-wallpaper-preview.jpg') no-repeat center center fixed; background-size:cover; min-height:100vh; display:flex; justify-content:center; align-items:center; padding:20px; }
    .box { background:rgba(42,42,80,0.95); padding:30px; border-radius:8px; border:4px solid #f3cd00; max-width:480px; width:100%; text-align:center; color:#fff; box-shadow:0 8px 16px rgba(0,0,0,0.5); }
    .box h2 { color:#f3cd00; margin-top:0; font-family:monospace; font-size:26px; }
    label { display:block; margin-bottom:5px; color:#ffeb3b; font-weight:bold; }
    input { width:100%; padding:10px; background:#1a1a2e; color:#fff; border:2px solid #f3cd00; border-radius:4px; font-size:14px; box-sizing:border-box; }
    .err { background:rgba(198,40,40,0.8); color:white; padding:10px; border-radius:4px; margin-bottom:15px; font-weight:bold; font-size:13px; border:2px solid #ff5555; }
    .pipe-btn { background:none; border:none; cursor:pointer; padding:0; margin:10px auto; display:block; }
    .pipe-btn img { width:90px; height:auto; }
    .links { display:flex; justify-content:space-between; border-top:1px solid #444; padding-top:15px; margin-top:15px; font-size:13px; }
    .links a { color:#ffeb3b; text-decoration:none; font-weight:bold; }
</style>
</head>
<body>
<div class="box">
    <h2>🔥 Sign In 🔥</h2>
    <?php if (!empty($error)): ?>
        <div class="err"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" style="text-align:left;">
        <div style="margin-bottom:15px;">
            <label>Username or Email:</label>
            <input type="text" name="login_input" value="<?php echo htmlspecialchars($_POST['login_input'] ?? ''); ?>" required>
        </div>
        <div style="margin-bottom:20px;">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="pipe-btn"><img src="../Tools and Guide/Green pipe.jpg" onerror="this.style.display='none'"></button>
        <div style="text-align:center; color:#ffeb3b; font-size:12px; margin-bottom:15px;">I-click ang Green Pipe para mag-login!</div>
        <div class="links">
            <a href="register.php">🎮 Register account</a>
            <a href="forgot_password.php">❓ Forgot Password?</a>
        </div>
    </form>
</div>
</body>
</html>