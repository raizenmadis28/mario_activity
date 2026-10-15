<?php
// users/Reset_Password.php
session_start();

$host = "localhost";
$user = "root";
$password = "@RBmadis28"; 
$dbname = "Mario_Activity";

$connection = @new mysqli($host, $user, $password, $dbname);
if ($connection->connect_error) {
    die("Connection Failed: " . $connection->connect_error);
}

// Siguraduhing dumaan muna sa forgot_password / naka-verify ang email
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $message = "OHH!! MAMMAMIAA!! GAME-OVER!! Lunan mo ang mga password fields!";
        $message_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "OHH!! MAMMAMIAA!! GAME-OVER!! Hindi nagtutugma ang mga password!";
        $message_type = "error";
    } else {
        // Pagsusuri ng haba at lakas ng password
        $length = strlen($new_password);
        $has_number = preg_match('/[0-9]/', $new_password);
        $has_char   = preg_match('/[a-zA-Z]/', $new_password);
        $has_symbol = preg_match('/[\W_]/', $new_password);

        if ($length < 6) {
            // 1. VERY WEAK PASSWORD
            $message = "OHHH MAAAMMAAAMIAA!! GAME-OVER!! YOU LOST A 1UP LIFE IN YOUR PASSWORD IS VERY WEAK";
            $message_type = "very_weak";
        } elseif ($length < 8) {
            // 2. WEAK PASSWORD
            $message = "OHHH MAAAMMAAAMIAA!! GAME-OVER!! YOU LOST A 1UP LIFE IN YOUR PASSWORD IS WEAK";
            $message_type = "weak";
        } elseif (!$has_number || !$has_symbol || !$has_char) {
            // 3. TOO WEAK PASSWORD
            $message = "OHHH MAAAMMAAAMIAA!! GAME-OVER!! YOU LOST A 1UP LIFE IN YOUR PASSWORD IS TOO WEAK";
            $message_type = "too_weak";
        } elseif ($length >= 8 && $has_number && $has_char && !$has_symbol) {
            // 4. MEDIUM PASSWORD (May letters at numbers pero walang special symbol)
            $message = "OHHH MAAAMMAAAMIAA!! GAME-OVER!! YOU LOST A 1UP LIFE IN YOUR PASSWORD IS MEDIUM";
            $message_type = "medium";
        } else {
            // Matagumpay na password reset (Malakas na password na may kasamang symbols)
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $email = $_SESSION['reset_email'];

            $stmt = $connection->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();
            $stmt->close();

            // Linisin ang session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_username']);

            $message = "YEHEYY!! YOUR PASSWORD HAS BEEN RESET!! GO BACK TO LOGIN!!";
            $message_type = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Mario Activity</title>
</head>
<body style="margin: 0; font-family: Arial, sans-serif; display: flex; flex-direction: column; min-height: 100vh;">

<?php include('../header.php'); ?>

<div style="flex: 1; background: url('../Mario Wallpaper/SuperStar_Road.png') no-repeat center center fixed; background-size: cover; display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: rgba(42, 42, 80, 0.95); padding: 30px; border-radius: 8px; border: 4px solid #f3cd00; max-width: 650px; width: 100%; text-align: center; color: #fff;">
        
        <!-- Daisy at Peach sa Reset Password -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div style="text-align: center; max-width: 130px;">
                <img src="../Tools and Guide/Daisy.jpg" alt="Princess Daisy" style="width: 80px; height: auto; border-radius: 6px;" onerror="this.style.display='none'">
                <div style="background: #1a1a2e; border: 2px solid #ffcc00; padding: 6px; border-radius: 6px; font-size: 10px; color: #ffeb3b; margin-top: 5px; font-weight: bold;">
                    HELLO!! CREATE A NEW PASSWORD WITH 8 or MORE CHARACTERS with Numbers, Characters and Symbols
                </div>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <h2 style="color: #f3cd00; margin: 0;">👑 Princess Daisy & Peach Reset Portal 💖</h2>
            </div>

            <div style="text-align: center; max-width: 130px;">
                <img src="../Tools and Guide/peach.jpg" alt="Princess Peach" style="width: 80px; height: auto; border-radius: 6px;" onerror="this.style.display='none'">
                <div style="background: #1a1a2e; border: 2px solid #ff99ff; padding: 6px; border-radius: 6px; font-size: 10px; color: #ff99ff; margin-top: 5px; font-weight: bold;">
                    THE PASSWORD MUST BE STRONG AND VERY STRONG NOT TOO WEAK, VERY WEAK, WEAK and MEDIUM
                </div>
            </div>
        </div>

        <!-- Mga Mensahe at Larawan ni Luigi para sa iba't ibang errors -->
        <?php if ($message_type == 'success'): ?>
            <div style="margin-bottom: 20px; background: rgba(46, 125, 50, 0.3); padding: 15px; border-radius: 6px; border: 2px solid #2e7d32;">
                <img src="../Tools and Guide/Luigi-Bros.png" alt="Luigi Success" style="width: 90px; height: auto; margin-bottom: 8px;" onerror="this.onerror=null; this.src='../Luigi Advance 4 Game-over.png';">
                <div style="font-weight: bold; color: #55ff55; font-size: 16px;">
                    <?php echo $message; ?>
                </div>
                <div style="margin-top: 15px;">
                    <a href="login.php" style="background: #2e7d32; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Mag-login Na</a>
                </div>
            </div>
        <?php elseif ($message_type == 'very_weak' || $message_type == 'weak' || $message_type == 'too_weak' || $message_type == 'medium'): ?>
            <div style="margin-bottom: 20px; background: rgba(198, 40, 40, 0.3); padding: 15px; border-radius: 6px; border: 2px solid #c62828;">
                <img src="../Funny Memes/Luigi Advance 4 Game-over.png" alt="Luigi Game Over" style="width: 90px; height: auto; margin-bottom: 8px;" onerror="this.onerror=null; this.src='../Luigi-Bros.png';">
                <div style="font-weight: bold; color: #ff5555; font-size: 14px; line-height: 1.4;">
                    <?php echo $message; ?>
                </div>
            </div>
        <?php elseif ($message_type == 'error'): ?>
            <div style="margin-bottom: 20px; background: rgba(198, 40, 40, 0.3); padding: 15px; border-radius: 6px; border: 2px solid #c62828;">
                <img src="../Funny Memes/Luigi Advance 4 Game-over.png" alt="Luigi Game Over" style="width: 90px; height: auto; margin-bottom: 8px;" onerror="this.onerror=null; this.src='../Luigi-Bros.png';">
                <div style="font-weight: bold; color: #ff5555; font-size: 15px;">
                    <?php echo $message; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($message_type != 'success'): ?>
        <form method="POST" style="text-align: left;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">New Password:</label>
                <input type="password" name="new_password" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Confirm New Password:</label>
                <input type="password" name="confirm_password" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; background: #e52521; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; font-size: 15px; cursor: pointer;">I-save ang Bagong Password</button>
        </form>
        <?php endif; ?>
        
        <div style="margin-top: 15px; text-align: center; border-top: 1px solid #444; padding-top: 15px;">
            <a href="login.php" style="color: #ffeb3b; font-size: 14px; text-decoration: none; font-weight: bold;">🎮 Bumalik sa Login</a>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>

</body>
</html>