<?php
// users/login.php
session_start();

// Database Connection
$host = "localhost";
$user = "root";
$password = "@RBmadis28"; 
$dbname = "Mario_Activity";

$connection = @new mysqli($host, $user, $password, $dbname);
if ($connection->connect_error) {
    die("Connection Failed: " . $connection->connect_error);
}

$error_type = ""; 
$error_message = "";
$redirect_to_index = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_input = $_POST['password'];

    if (empty($username) || empty($password_input)) {
        $error_type = "empty";
        $error_message = "OHHH MAMAMIAA!! GAME-OVERR!! YOU LOST A 1UP Life na Punan ang lahat ng espasyo para makapag-sign in!";
    } else {
        $stmt = $connection->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password_input, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                
                $error_type = "success";
                $error_message = "YEHEYY!! Nakapasok ka na sa Mario Inventory System!";
                $redirect_to_index = true;
            } else {
                $error_type = "wrong";
                $error_message = "Gwahahaha! Maling password, hindi mo maloloko ang kaharian ko!";
            }
        } else {
            $error_type = "wrong";
            $error_message = "Gwahahaha! Walang ganyang Username o Email sa aming listahan!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In - Mario Activity</title>
    <?php if ($redirect_to_index): ?>
        <meta http-equiv="refresh" content="2;url=../index.php">
    <?php endif; ?>
</head>
<body style="margin: 0; font-family: Arial, sans-serif; display: flex; flex-direction: column; min-height: 100vh;">

<?php include '../header.php'; ?>

<div style="flex: 1; background: url('../Mario Wallpaper/mario-super-mario-bros-3-wallpaper-preview.jpg') no-repeat center center fixed; background-size: cover; display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: rgba(42, 42, 80, 0.95); padding: 30px; border-radius: 8px; border: 4px solid #f3cd00; max-width: 450px; width: 100%; text-align: center; color: #fff;">
        
        <h2 style="color: #f3cd00; margin-top: 0;">🔥 Sign In</h2>

        <?php if (!empty($error_type)): ?>
            <?php 
                $bg_color = ($error_type === 'success') ? 'rgba(46, 125, 50, 0.9)' : 'rgba(198, 40, 40, 0.9)';
            ?>
            <div style="background: <?php echo $bg_color; ?>; border: 2px solid #ffeb3b; color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; text-align: left;">
                <?php if ($error_type === 'empty'): ?>
                    <img src="../Funny Memes/Mario-Gameover-Pic.png" alt="Mario Game Over" style="width: 45px; height: auto; flex-shrink: 0;" onerror="this.style.display='none'">
                <?php elseif ($error_type === 'wrong'): ?>
                    <img src="../Funny Memes/Bowser.png" alt="Bowser Laugh" style="width: 45px; height: auto; flex-shrink: 0;" onerror="this.style.display='none'">
                <?php elseif ($error_type === 'success'): ?>
                    <img src="../Tools and Guide/Luigi-Bros.png" alt="Luigi Success" style="width: 45px; height: auto; flex-shrink: 0;" onerror="this.style.display='none'">
                <?php endif; ?>
                
                <div style="font-weight: bold; font-size: 13px; line-height: 1.4;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$redirect_to_index): ?>
            <form method="POST" style="text-align: left;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Username or Email:</label>
                    <input type="text" name="username" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px;" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Password:</label>
                    <input type="password" name="password" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px;">
                </div>

                <div style="text-align: center; margin-bottom: 20px;">
                    <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;" title="Pumasok sa Green Pipe!">
                        <img src="../Tools and Guide/Green pipe.jpg" alt="Green Pipe Login" style="width: 90px; height: auto; display: block; margin: 0 auto; border-radius: 4px;" onerror="this.onerror=null; this.src='../green-pipe.png';">
                    </button>
                    <div style="font-size: 12px; color: #ffeb3b; margin-top: 5px; font-family: monospace;">I-click ang Green Pipe para mag-login!</div>
                </div>

                <div style="display: flex; justify-content: space-between; border-top: 1px solid #444; padding-top: 15px; font-size: 13px;">
                    <a href="register.php" style="color: #ffeb3b; text-decoration: none; font-weight: bold;">🎮 Register account</a>
                    <a href="forgot_password.php" style="color: #ffeb3b; text-decoration: none; font-weight: bold;">❓ Forgot Password?</a>
                </div>
            </form>
        <?php else: ?>
            <div style="font-size: 14px; color: #ffeb3b; font-family: monospace;">Ikinakarga ang Mario Inventory System... Sandali lamang!</div>
        <?php endif; ?>

    </div>
</div>

<?php include '../footer.php'; ?>

</body>
</html>