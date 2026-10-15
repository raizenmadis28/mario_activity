<?php
// users/forgot_password.php
session_start();

$host = "localhost";
$user = "root";
$password = "@RBmadis28"; 
$dbname = "Mario_Activity";

$connection = @new mysqli($host, $user, $password, $dbname);
if ($connection->connect_error) {
    die("Connection Failed: " . $connection->connect_error);
}

$message = "";
$message_type = "";
$simulated_gmail = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['recovery_email']);
    $username = trim($_POST['recovery_username']);
    
    if (empty($email) || empty($username)) {
        $message = "OHH!! MAMMAMIAA!! GAME-OVER!! YOU LOST A 1-UP LIFE! Please Fill Up all Credentials FIRST!!";
        $message_type = "empty_error";
    } else {
        $stmt = $connection->prepare("SELECT id FROM users WHERE email = ? AND username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Gumawa ng random verification code at itabi sa session kasama ang timestamp
            $verification_code = rand(100000, 999999);
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_username'] = $username;
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['code_time'] = time(); // Para sa 2 minutes expiration check

            $message = "YAHOO!! Nahanap ang iyong Email at Username!";
            $message_type = "success";

            // Simulated Gmail Inbox Reply ayon sa detalye
            $simulated_gmail = "
                <div style='background: #ffffff; color: #000; border: 2px solid #2e7d32; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: left;'>
                    <h4 style='color: #2e7d32; margin-top: 0;'>📥 Simulated Gmail / Inbox Reply</h4>
                    <p><strong>To:</strong> " . htmlspecialchars($email) . "</p>
                    <p><strong>Subject:</strong> RaizenMadis28 Login System Reset Password</p>
                    <hr>
                    <p>Hello <strong>" . htmlspecialchars($username) . "</strong>,</p>
                    <p>Nakatanggap kami ng kahilingan na i-reset ang iyong password.</p>
                    <p><strong>Verification Code:</strong> <span style='font-size: 18px; color: #d32f2f; font-weight: bold;'>$verification_code</span></p>
                    <p>Mangyaring ilagay ang numerong ito sa pahina ng pag-verify.</p>
                    <a href='verifications.php' style='display: inline-block; background: #e52521; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;'>I-reset ang Password ngayon</a>
                </div>
            ";
        } else {
            $message = "OHH MAAMAA MIAA!!!! GAME-OVER! YOU LOST A 1UP-LIFE FOR NOT EMAIL HAS FOUND!!!";
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Mario Activity</title>
</head>
<body style="margin: 0; font-family: Arial, sans-serif; display: flex; flex-direction: column; min-height: 100vh;">

<?php include('../header.php'); ?>

<div style="flex: 1; background: url('../Mario Wallpaper/mario-world-map.jpg') no-repeat center center fixed; background-size: cover; display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: rgba(42, 42, 80, 0.95); padding: 30px; border-radius: 8px; border: 4px solid #f3cd00; max-width: 650px; width: 100%; text-align: center; color: #fff;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div style="text-align: center; max-width: 130px;">
                <img src="../Tools and Guide/toad.png" alt="Toad" style="width: 80px; height: auto; border-radius: 6px;">
                <div style="background: #1a1a2e; border: 2px solid #f3cd00; padding: 6px; border-radius: 6px; font-size: 11px; color: #ffeb3b; margin-top: 5px; font-weight: bold;">
                    HEYY!! I-verify natin ang iyong email!
                </div>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <h2 style="color: #f3cd00; margin: 0;">🍄 Toad & Toadette Recovery Hub ❓</h2>
            </div>

            <div style="text-align: center; max-width: 130px;">
                <img src="../Tools and Guide/toadette.png" alt="Toadette" style="width: 80px; height: auto; border-radius: 6px;">
                <div style="background: #1a1a2e; border: 2px solid #ff77ff; padding: 6px; border-radius: 6px; font-size: 11px; color: #ff99ff; margin-top: 5px; font-weight: bold;">
                    HELLO!! Hanapin natin ang iyong account!
                </div>
            </div>
        </div>

        <?php if ($message_type == 'success'): ?>
            <div style="margin-bottom: 20px; background: rgba(46, 125, 50, 0.3); padding: 15px; border-radius: 6px; border: 2px solid #2e7d32;">
                <img src="../Tools and Guide/Super-Mario.png" alt="Mario Success" style="width: 90px; height: auto; margin-bottom: 8px;">
                <div style="font-weight: bold; color: #55ff55; font-size: 16px;">
                    <?php echo $message; ?>
                </div>
            </div>
            <?php echo $simulated_gmail; ?>
            <div style="margin-top: 15px;">
                <a href="verifications.php" style="display: inline-block; background: #388e3c; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">Pumunta sa Verifications Page ➔</a>
            </div>
        <?php elseif ($message_type == 'empty_error' || $message_type == 'error'): ?>
            <div style="margin-bottom: 20px; background: rgba(198, 40, 40, 0.3); padding: 15px; border-radius: 6px; border: 2px solid #c62828;">
                <img src="../Funny Memes/Mario Advance 4 Game-over.png" alt="Mario Game Over" style="width: 90px; height: auto; margin-bottom: 8px;">
                <div style="font-weight: bold; color: #ff5555; font-size: 15px;">
                    <?php echo $message; ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" style="text-align: left;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Email Address:</label>
                <input type="email" name="recovery_email" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px; box-sizing: border-box;" value="<?php echo isset($_POST['recovery_email']) ? htmlspecialchars($_POST['recovery_email']) : ''; ?>">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Username:</label>
                <input type="text" name="recovery_username" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px; box-sizing: border-box;" value="<?php echo isset($_POST['recovery_username']) ? htmlspecialchars($_POST['recovery_username']) : ''; ?>">
            </div>

            <button type="submit" style="width: 100%; background: #e52521; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; font-size: 15px; cursor: pointer;">I-verify ang Email / Hanapin ang Account</button>
        </form>
        
        <div style="margin-top: 15px; text-align: center; border-top: 1px solid #444; padding-top: 15px;">
            <a href="login.php" style="color: #ffeb3b; font-size: 14px; text-decoration: none; font-weight: bold;">🎮 Bumalik sa Login</a>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>

</body>
</html>