<?php
// sign-in.php
require_once '../Model/DB_Model.php';

// I-handle kung `$conn` man ang gamit sa DB_Model.php
if (!isset($connection) && isset($conn)) {
    $connection =$conn;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['login_input']);
    $password =$_POST['password'];

    if (empty($login_input) || empty($password)) {$error = "MAMAMIAA!! Punan ang lahat ng espasyo para makapasok sa Level!";
    } elseif (isset($connection)) {
        $stmt =$connection->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $login_input, $login_input);$stmt->execute();
        $result =$stmt->get_result();

        if ($result->num_rows === 1) {
            $user =$result->fetch_assoc();
            if (password_verify($password,$user['password'])) {
                session_start();
                $_SESSION['user_id'] =$user['id'];
                $_SESSION['username'] =$user['username'];
                $_SESSION['role'] =$user['role'];

                // Itinama ang redirection palabas ng users folder
                if (strtolower($user['role']) === 'admin') {
                    header("Location: ../admin/admin_dashboard.php");
                } else {
                    header("Location: ../index.php");
                }
                exit();
            } else {
                $error = "GAME OVER! Mali ang iyong Password!";
            }
        } else {
            $error = "MAMAMIAA! Walang rehistradong Username o Email na ganyan!";
        }
        $stmt->close();
    } else {
        $error = "May problema sa Database Connection!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In - Mario Activity</title>
</head>
<body style="margin: 0; font-family: Arial, sans-serif; display: flex; flex-direction: column; min-height: 100vh;">

<?php include('../header.php'); ?>

<!-- Itinutok sa tamang folder na Mario Wallpaper -->
<div style="flex: 1; background: url('../Mario Wallpaper/mario-super-mario-bros-3-wallpaper-preview.jpg') no-repeat center center fixed; background-size: cover; display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: rgba(42, 42, 80, 0.95); padding: 30px; border-radius: 8px; border: 4px solid #f3cd00; max-width: 480px; width: 100%; text-align: center; color: #fff; box-shadow: 0 8px 16px rgba(0,0,0,0.5);">
        
        <h2 style="color: #f3cd00; margin-top: 0; font-family: monospace; font-size: 26px;">🔥 Sign In 🔥</h2>

        <?php if (!empty($error)): ?>
            <div style="background: rgba(198, 40, 40, 0.8); color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-weight: bold; font-size: 13px; border: 2px solid #ff5555;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="text-align: left;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b; font-weight: bold;">Username or Email:</label>
                <input type="text" name="login_input" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px; font-size: 14px;" value="<?php echo isset($_POST['login_input']) ? htmlspecialchars($_POST['login_input']) : ''; ?>" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b; font-weight: bold;">Password:</label>
                <input type="password" name="password" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px; font-size: 14px;" required>
            </div>

            <div style="text-align: center; margin-bottom: 20px;">
                <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;" title="I-click ang Green Pipe para mag-login!">
                    <img src="../Tools and Guide/Green pipe.jpg" alt="Green Pipe Login" style="width: 90px; height: auto; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'" onerror="this.onerror=null; this.src='Green pipe.jpg';">
                </button>
                <div style="color: #ffeb3b; font-size: 12px; margin-top: 5px; font-family: monospace;">I-click ang Green Pipe para mag-login!</div>
            </div>

            <div style="display: flex; justify-content: space-between; border-top: 1px solid #444; padding-top: 15px; font-size: 13px;">
                <a href="register.php" style="color: #ffeb3b; text-decoration: none; font-weight: bold;">🎮 Register account</a>
                <a href="forgot_password.php" style="color: #ff99ff; text-decoration: none; font-weight: bold;">❓ Forgot Password?</a>
            </div>
        </form>
    </div>
</div>

<?php include('../footer.php'); ?>

</body>
</html>