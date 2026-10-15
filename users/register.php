<?php
// register.php - Raizeness Inventory Anime System
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Model/DB_Model.php';

if (!isset($connection) && isset($conn)) {
    $connection = $conn;
}

// Awtomatikong gawan ng tables kung hindi pa umiiral
if (isset($connection)) {
    $connection->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL,
        date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $connection->query("CREATE TABLE IF NOT EXISTS admin_list (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_username VARCHAR(255) NOT NULL,
        date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $connection->query("CREATE TABLE IF NOT EXISTS customer_list (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_username VARCHAR(255) NOT NULL,
        date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

$error = "";
$success_script = "";
$error_type = ""; // Para malaman kung Game Over o Bowser alert ang ipapakita

if (isset($_POST['register']) && isset($connection)) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $raw_password = $_POST['password'];
    $role = $_POST['role'];

    // I-check kung blangko ang mga fields (Game-over 1UP alert)
    if (empty($username) || empty($email) || empty($raw_password) || empty($role)) {
        $error = "OHHH!! MAAMMAAMIAA!! GAME-OVER!! YOU LOST 1UP Life FOR FILLING ALL THE REGISTER FIELDS FIRSTT!!";
        $error_type = "gameover";
    } else {
        $password = password_hash($raw_password, PASSWORD_DEFAULT);

        // I-check kung umiiral na ang username (Bowser alert)
        $check = $connection->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "GWAHAHAHAHAHAHA!! MERON NA USERNAME NA GINAMIT TIGNAN MO MUNA MABUTI!!";
            $error_type = "bowser";
        } else {
            $stmt = $connection->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password, $role);
            
            if ($stmt->execute()) {
                // Kung Admin, idagdag sa admin_list at i-redirect sa admin/admin_list.php
                if ($role == 'Admin') {
                    $adm_stmt = $connection->prepare("INSERT INTO admin_list (admin_username) VALUES (?)");
                    $adm_stmt->bind_param("s", $username);
                    $adm_stmt->execute();
                    $adm_stmt->close();

                    $success_script = "<script>alert('YAHOO!! YOUR USERNAME OF ADMIN WAS DIRECTED INTO ADMIN LIST.PHP'); window.location.href='../admin/admin_list.php';</script>";
                } 
                // Kung Customer, idagdag sa customer_list at i-redirect sa admin/customer_list.php
                else {
                    $cust_stmt = $connection->prepare("INSERT INTO customer_list (customer_username) VALUES (?)");
                    $cust_stmt->bind_param("s", $username);
                    $cust_stmt->execute();
                    $cust_stmt->close();

                    $success_script = "<script>alert('YAHOO!! YOUR USERNAME OF CUSTOMER WAS DIRECTED INTO CUSTOMER LIST.PHP'); window.location.href='../admin/customer_list.php';</script>";
                }
            } else {
                $error = "May error sa pagpaparehistro.";
                $error_type = "gameover";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<html>
<head>
<title>Register Account - Raizeness Inventory</title>
<style>
    body {
        background-image: url('../Mario Wallpaper/Mario-plays.png');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
        font-family: "Comic Sans MS", "Chalkboard SE", sans-serif;
        color: #fff;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .main-content {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .register-container {
        background-color: rgba(40, 44, 79, 0.95);
        border: 3px solid #ffeb3b;
        border-radius: 15px;
        padding: 25px 30px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0px 8px 16px rgba(0,0,0,0.5);
    }
    h2 {
        color: #ffeb3b;
        text-align: center;
        text-shadow: 2px 2px #d32f2f;
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 24px;
    }
    label {
        font-weight: bold;
        color: #ffeb3b;
        display: block;
        margin-bottom: 3px;
        font-size: 13px;
    }
    input, select {
        width: 100%;
        padding: 8px;
        margin-bottom: 12px;
        border: 2px solid #ffeb3b;
        border-radius: 6px;
        background-color: #1a1e36;
        color: #fff;
        font-size: 13px;
    }
    .btn-register {
        width: 100%;
        background-color: #388e3c;
        color: white;
        padding: 10px;
        border: 2px solid #ffeb3b;
        border-radius: 6px;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
        text-align: center;
        margin-top: 5px;
    }
    .btn-register:hover {
        background-color: #2e7d32;
    }
    .login-link {
        text-align: center;
        margin-top: 12px;
        font-size: 13px;
    }
    .login-link a {
        color: #ffeb3b;
        text-decoration: none;
        font-weight: bold;
    }
    .login-link a:hover {
        text-decoration: underline;
    }
    /* Error / Alert Box Style */
    .gameover-alert {
        background-color: rgba(198, 40, 40, 0.95);
        border: 2px dashed #ffeb3b;
        color: white;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-align: left;
    }
    .gameover-alert img {
        width: 45px;
        height: auto;
        flex-shrink: 0;
    }
    .gameover-alert span {
        font-weight: bold;
        font-size: 12px;
        line-height: 1.3;
        text-shadow: 1px 1px #000;
    }
</style>
</head>
<body>

<?php include('../header.php');?>

<?php echo $success_script; ?>

<div class="main-content">
    <div class="register-container">
        <h2>🍄 Register Account 🍄</h2>

        <?php if (!empty($error)): ?>
            <div class="gameover-alert">
                <?php if ($error_type == 'bowser'): ?>
                    <img src="../Funny Memes/Bowser.png" alt="Bowser" onerror="this.src='../Funny Memes/bowser.png'">
                <?php else: ?>
                    <img src="../Funny Memes/Mario Advance 4 Game-over.png" alt="Game Over" onerror="this.style.display='none'">
                <?php endif; ?>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            
            <label>Password:</label>
            <input type="password" name="password">
            
            <label>Role:</label>
            <select name="role">
                <option value="">-- Piliin ang Role --</option>
                <option value="Customer" <?php if(isset($_POST['role']) && $_POST['role']=='Customer') echo 'selected'; ?>>Customer</option>
                <option value="Admin" <?php if(isset($_POST['role']) && $_POST['role']=='Admin') echo 'selected'; ?>>Admin</option>
            </select>
            
            <button type="submit" name="register" class="btn-register">Lumikha ng Account (Register)</button>
        </form>

        <div class="login-link">
            <a href="sign-in.php">🔥 May account na? Mag-sign in</a>
        </div>
    </div>
</div>

<?php include('../footer.php');?>

</body>
</html>