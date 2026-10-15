<?php
// users/verifications.php
session_start();

$message = "";
$message_type = "";

if (!isset($_SESSION['verification_code'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_code = trim($_POST['verification_code']);
    $current_time = time();
    $code_time = $_SESSION['code_time'] ?? 0;

    // Suriin kung lumampas na sa 2 minuto (120 segundo) ang expiration sa server side
    if (($current_time - $code_time) > 120) {
        $message = "AHAHAHAHAHA!! EXPIRE ANG LINK NG VERIFICATION CODE MO INTAYIN MO NA LANG NG 2 Minutes Bago ka magverify ng CODE!";
        $message_type = "expire";
    } elseif ($input_code == $_SESSION['verification_code']) {
        // Tagumpay
        $_SESSION['verified_success'] = true;
        $message = "YEHEYY!! MAARING PUMUNTA KA SA RESET PASSWORD!";
        $message_type = "success";
    } else {
        $message = "OHH MAAMAA MIAA!! Maling Verification Code! Subukan muli!";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <title>Verification Code - Mario Activity</title>
    <script>
        // JavaScript 2-Minute Timer para sa Expiration
        document.addEventListener("DOMContentLoaded", function() {
            var timeLeft = 120; // 2 minuto sa segundo
            var timerDisplay = document.getElementById("timer");
            var formContainer = document.getElementById("verification-form-container");
            var bowserAlert = document.getElementById("bowser-alert");

            var countdown = setInterval(function() {
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                
                seconds = seconds < 10 ? "0" + seconds : seconds;
                if (timerDisplay) {
                    timerDisplay.innerHTML = minutes + ":" + seconds;
                }

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    if (formContainer) {
                        formContainer.style.display = "none";
                    }
                    if (bowserAlert) {
                        bowserAlert.style.display = "block";
                    }
                }
                timeLeft--;
            }, 1000);
        });
    </script>
</head>
<body style="margin: 0; font-family: Arial, sans-serif; display: flex; flex-direction: column; min-height: 100vh;">

<?php include('../header.php'); ?>

<div style="flex: 1; background: url('../Mario Wallpaper/Mario 3D Land.jpeg') no-repeat center center fixed; background-size: cover; display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: rgba(42, 42, 80, 0.95); padding: 30px; border-radius: 8px; border: 4px solid #f3cd00; max-width: 550px; width: 100%; text-align: center; color: #fff;">
        
        <h2 style="color: #f3cd00; margin-top: 0;">🔐 RaizenMadis28 Verification Code</h2>
        <p style="font-size: 14px; color: #ddd;">Tingnan ang iyong Simulated Gmail Inbox at ilagay ang Verification Numbers.</p>
        
        <!-- Timer Counter Display -->
        <div style="margin-bottom: 15px; font-weight: bold; color: #ffeb3b; font-size: 16px;">
            ⏳ Oras bago mag-expire: <span id="timer" style="color: #ff5555; font-size: 18px;">2:00</span>
        </div>

        <!-- Awtomatikong Bowser Jr Alert kapag lumipas ang 2 minuto -->
        <div id="bowser-alert" style="display: none; margin-bottom: 20px; background: rgba(198, 40, 40, 0.4); padding: 15px; border-radius: 6px; border: 2px solid #c62828;">
            <img src="../Funny Memes/bowser jr.png" alt="Bowser Jr" style="width: 90px; height: auto; border-radius: 6px; margin-bottom: 8px;">
            <div style="font-weight: bold; color: #ff6b6b; font-size: 14px;">
                AHAHAHAHAHA!! EXPIRE ANG LINK NG VERIFICATION CODE MO INTAYIN MO NA LANG NG 2 Minutes Bago ka magverify ng CODE!
            </div>
        </div>

        <?php if ($message_type == 'expire'): ?>
            <div style="margin-bottom: 20px; background: rgba(198, 40, 40, 0.4); padding: 15px; border-radius: 6px; border: 2px solid #c62828;">
                <img src="../Funny Memes/bowser jr.png" alt="Bowser Jr" style="width: 90px; height: auto; border-radius: 6px; margin-bottom: 8px;">
                <div style="font-weight: bold; color: #ff6b6b; font-size: 14px;">
                    <?php echo $message; ?>
                </div>
            </div>
        <?php elseif ($message_type == 'success'): ?>
            <div style="margin-bottom: 20px; background: rgba(46, 125, 50, 0.4); padding: 15px; border-radius: 6px; border: 2px solid #2e7d32;">
                <img src="../Tools and Guide/Luigi-Bros.png" alt="Luigi Success" style="width: 90px; height: auto; border-radius: 6px; margin-bottom: 8px;" onerror="this.onerror=null; this.src='../Luigi-Bros.png';">
                <div style="font-weight: bold; color: #55ff55; font-size: 15px;">
                    <?php echo $message; ?>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <a href="Reset_Password.php" style="display: inline-block; background: #e52521; color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 15px;">I-reset ang Password Na Ginamitan ng Mario 3D Land ➔</a>
            </div>
        <?php elseif ($message_type == 'error'): ?>
            <div style="margin-bottom: 20px; background: rgba(198, 40, 40, 0.3); padding: 15px; border-radius: 6px; border: 2px solid #c62828;">
                <img src="../Funny Memes/Mario Advance 4 Game-over.png" alt="Error" style="width: 80px; height: auto; margin-bottom: 8px;">
                <div style="font-weight: bold; color: #ff5555; font-size: 14px;">
                    <?php echo $message; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($message_type != 'success'): ?>
            <div id="verification-form-container">
                <!-- Pagpapakita ng Mario sa pagsusumite kasama ang tamang path ng larawan -->
                <div style="margin-bottom: 15px;">
                    <img src="../Tools and Guide/Super-Mario.png" alt="Mario" style="width: 70px; height: auto;" onerror="this.onerror=null; this.src='../Super-Mario.png';">
                    <p style="font-size: 12px; color: #ffeb3b; margin: 5px 0 0 0;">YAHOO!! YOUR INBOX HAS BEEN SENT! PLEASE DIRECT TO VERIFICATION CODE</p>
                </div>

                <form method="POST" style="text-align: left;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Please Fill in the Verification numbers:</label>
                        <input type="text" name="verification_code" placeholder="Ilagay ang verification code" required style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px; box-sizing: border-box; text-align: center; font-size: 18px; letter-spacing: 2px;">
                    </div>

                    <button type="submit" style="width: 100%; background: #388e3c; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; font-size: 15px; cursor: pointer;">I-verify ang Code</button>
                </form>
            </div>
        <?php endif; ?>

        <div style="margin-top: 20px; text-align: center; border-top: 1px solid #444; padding-top: 15px;">
            <a href="forgot_password.php" style="color: #ffeb3b; font-size: 14px; text-decoration: none; font-weight: bold;">⬅️ Bumalik sa Forgot Password</a>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>

</body>
</html>