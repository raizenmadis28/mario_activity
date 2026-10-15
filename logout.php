<?php
session_start();

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'return_inventory') {
    $show_return = true;
} elseif ($action === 'abort_login') {
    $show_laugh = true;
} else {
    // I-clear muna ang session kapag pumasok sa logout page
    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bowser Jr. Logout Gateway - Raizeness Inventory Anime System</title>
    <?php if (isset($show_return) && $show_return): ?>
        <meta http-equiv="refresh" content="10;url=index.php">
    <?php elseif (isset($show_laugh) && $show_laugh): ?>
        <meta http-equiv="refresh" content="10;url=users/login.php">
    <?php endif; ?>
</head>
<body style="margin: 0; font-family: 'Courier New', Courier, monospace; background: url('Mario Wallpaper/NSMBUMushroomKingdom.png') no-repeat center center fixed; background-size: cover; height: 100vh; display: flex; justify-content: center; align-items: center; color: #fff;">

    <div style="background: rgba(20, 20, 40, 0.95); border: 4px solid #ffcc00; padding: 30px; border-radius: 12px; max-width: 500px; width: 100%; text-align: center; box-shadow: 0 0 25px rgba(255, 204, 0, 0.5);">
        
        <!-- Bowser Jr Image -->
        <img src="Funny Memes/bowser jr.png" alt="Bowser Jr." style="width: 90px; height: 90px; border-radius: 50%; border: 3px solid #ff3333; object-fit: cover; margin-bottom: 15px;" onerror="this.style.display='none'">

        <?php if (isset($show_return) && $show_return): ?>
            <!-- GREEN PIPE RETURN SCREEN -->
            <div style="background: rgba(0, 100, 0, 0.7); border: 2px solid #4CAF50; padding: 20px; border-radius: 10px;">
                <p style="font-size: 15px; color: #a5d6a7; font-weight: bold; line-height: 1.5; margin-bottom: 15px;">
                    AWWW!! Binalik kita sa Mario Inventory System para sa pagpatuloy mong Inventory, Admin at Customer!!
                </p>
                <img src="Green pipe.jpg" alt="Green Pipe" style="width: 70px; height: auto; display: block; margin: 0 auto 15px auto; border: 2px solid #fff; border-radius: 4px;" onerror="this.style.display='none'">
                <p style="font-size: 14px; color: #fff; margin-bottom: 15px;">
                    Redirecting to System in <span id="timer" style="color: #ffeb3b; font-weight: bold;">10</span> seconds...
                </p>
                <a href="index.php" style="display: inline-block; background: #2e7d32; color: #fff; padding: 8px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; border: 2px solid #000;">Pumasok sa Pipe Now</a>
            </div>

            <script>
                let timeLeft = 10;
                const timerElement = document.getElementById('timer');
                const countdown = setInterval(() => {
                    timeLeft--;
                    timerElement.textContent = timeLeft;
                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        window.location.href = 'index.php';
                    }
                }, 1000);
            </script>

        <?php elseif (isset($show_laugh) && $show_laugh): ?>
            <!-- RED LAVA ABORT SCREEN -->
            <h2 style="color: #ff3333; margin-top: 0; text-shadow: 2px 2px #000;">AHAHAHAHHA!! 🦖🔥</h2>
            <p style="font-size: 18px; color: #ffeb3b; font-weight: bold; line-height: 1.5; margin-bottom: 15px;">
                NASA LOGIN KA NA NGAUN!
            </p>
            <p style="font-size: 14px; color: #fff; margin-bottom: 20px;">
                Direct Link in <span id="timer" style="color: #ff3333; font-weight: bold;">10</span> seconds...
            </p>
            <a href="users/login.php" style="display: inline-block; background: #28a745; color: #fff; padding: 8px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; border: 2px solid #000;">Go to Login Now</a>

            <script>
                let timeLeft = 10;
                const timerElement = document.getElementById('timer');
                const countdown = setInterval(() => {
                    timeLeft--;
                    timerElement.textContent = timeLeft;
                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        window.location.href = 'users/login.php';
                    }
                }, 1000);
            </script>

        <?php else: ?>
            <!-- MAIN LOGOUT GATEWAY SELECTION -->
            <h2 style="color: #ff3333; margin-top: 0; text-shadow: 2px 2px #000;">⚠️ BOWSER JR. LOGOUT PHASE ⚠️</h2>
            
            <p style="font-size: 15px; color: #ffeb3b; line-height: 1.5; margin-bottom: 25px;">
                "Gwehehehhehe! Gusto mo na bang lumabas o magbago ang iyong isip sa kaharian?"
            </p>

            <div style="display: flex; justify-content: space-around; gap: 15px;">
                
                <!-- Direksyon 1: Green Pipe (Bumalik sa Inventory System) -->
                <div style="flex: 1; background: rgba(0, 100, 0, 0.6); border: 2px solid #4CAF50; padding: 15px; border-radius: 8px;">
                    <p style="font-size: 12px; color: #a5d6a7; margin-top: 0; font-weight: bold;">AWWW!! Binalik kita sa Mario Inventory System para sa pagpatuloy mong Inventory, Admin at Customer!!</p>
                    <a href="logout.php?action=return_inventory" style="text-decoration: none;">
                        <img src="Tools and Guide/Green pipe.jpg" alt="Green Pipe" style="width: 60px; height: auto; display: block; margin: 0 auto; border: 2px solid #fff; border-radius: 4px;" onerror="this.style.display='none'">
                        <span style="display: inline-block; margin-top: 8px; background: #2e7d32; color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold;">Pumasok sa Pipe</span>
                    </a>
                </div>

                <!-- Direksyon 2: Red Lava (Abort / Babalik sa Login) -->
                <div style="flex: 1; background: rgba(139, 0, 0, 0.6); border: 2px solid #f44336; padding: 15px; border-radius: 8px;">
                    <p style="font-size: 12px; color: #ffcdd2; margin-top: 0; font-weight: bold;">Gwahahaha! Mag-abort at bumalik sa Sign-In Login page sa pamamagitan ng Red Lava!</p>
                    <a href="logout.php?action=abort_login" style="text-decoration: none;">
                        <img src="Tools and Guide/Red Lava.png" alt="Red Lava" style="width: 60px; height: 40px; display: block; margin: 0 auto; border: 2px solid #fff; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                        <span style="display: inline-block; margin-top: 8px; background: #c62828; color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold;">Abort (Login)</span>
                    </a>
                </div>

            </div>
        <?php endif; ?>

    </div>

</body>
</html>