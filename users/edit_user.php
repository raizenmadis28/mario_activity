<?php
// users/edit_user.php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../Model/DB_Model.php';
include __DIR__ . '/../header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = "";
$success = "";

// Admin can edit anyone; customer can only edit self
$current = current_user();
$is_admin = strcasecmp($current['role'], 'Admin') === 0;
if (!$is_admin && $id !== (int)$current['id']) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (!$is_admin) $role = $current['role'];

    $update = $connection->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
    $update->bind_param("sssi", $username, $email, $role, $id);
    if ($update->execute()) {
        $success = "YEHEYY!! Account Updated Successfully!";
        if ($id === (int)$current['id']) {
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
        }
    } else {
        $error = "OHHH, MAMMAMIAA!! Update Failed!";
    }
    $update->close();
}

$stmt = $connection->prepare("SELECT username, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<div style='color:white; text-align:center; padding:50px;'>User not found! <a href='../index.php' style='color:#ffeb3b;'>Back to Dashboard</a></div>";
    include __DIR__ . '/../footer.php';
    exit();
}
?>

<div style="flex:1; background: url('../Mario Wallpaper/mario-super-mario-bros-3-wallpaper-preview.jpg') no-repeat center center fixed; background-size: cover; display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: rgba(42, 42, 80, 0.95); padding: 30px; border-radius: 8px; border: 4px solid #f3cd00; max-width: 450px; width: 100%; text-align: center; color: #fff;">
        <h2 style="color: #f3cd00; margin-top: 0;">✏️ Edit User Account</h2>

        <?php if (!empty($error)): ?>
            <div style="background: #c62828; color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-weight: bold;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="background: #2e7d32; color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-weight: bold;">
                <img src="../Tools and Guide/Super-Mario.png" alt="Success" style="width: 50px; display: block; margin: 0 auto 5px auto;" onerror="this.style.display='none'">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="text-align: left;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #ffeb3b;">Role:</label>
                <select name="role" <?php echo $is_admin ? '' : 'disabled'; ?> style="width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: 2px solid #f3cd00; border-radius: 4px;">
                    <option value="Customer" <?php echo (strcasecmp($user['role'],'Customer')==0) ? 'selected' : ''; ?>>Customer</option>
                    <option value="Admin" <?php echo (strcasecmp($user['role'],'Admin')==0) ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" style="width: 100%; background: #e52521; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; font-size: 15px; cursor: pointer; margin-bottom: 15px;">Update Account</button>
            <div style="text-align: center;">
                <a href="../index.php" style="color: #ffeb3b; text-decoration: none; font-weight: bold; font-size: 13px;">🎮 Bumalik sa Dashboard</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>