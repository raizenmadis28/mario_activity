<?php
// admin_list.php (Nasa loob ng admin folder)

// 1. Database Connection (MySQLi)
$host     = "localhost";
$user     = "root";
$password = "@RBmadis28";
$database = "Mario_Activity";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksyon nabigo: " . $conn->connect_error);
}

// Auto-create sa `admins` table kung wala pa sa database
$conn->query("CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Awtomatikong i-sync ang mga admin mula sa users table patungo sa admins table gamit ang tamang column na date_added
$sync_check = $conn->query("SELECT COUNT(*) as total FROM admins");
if ($sync_check) {
    $sync_data = $sync_check->fetch_assoc();
    if ($sync_data['total'] == 0) {
        $conn->query("INSERT INTO admins (username, password, created_at) SELECT username, password, date_added FROM users WHERE role = 'Admin'");
    }
}

$message = ''; 
$error_type = '';
$is_edit = false;
$edit_id = 0;
$edit_username = '';

// Kuhanin ang notification message mula sa URL
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'updated') $message = "Tagumpay na na-update ang datos ng Admin!";
    if ($_GET['msg'] === 'added') $message = "Tagumpay na naidagdag ang bagong Admin!";
    if ($_GET['msg'] === 'deleted') $message = "Tagumpay na nabura ang Admin!";
}

// 2. KUNIN ANG DATOS NG ADMIN KAPAG PININDOT ANG "EDIT"
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($admin_data = $res->fetch_assoc()) {
        $is_edit = true;
        $edit_username = $admin_data['username'];
    }
    $stmt->close();
}

// 3. PAGPROSESO SA PAG-ADD O PAG-UPDATE NG ADMIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_admin'])) {
    $admin_id   = intval($_POST['admin_id'] ?? 0);
    $username   = trim($_POST['username'] ?? '');
    $pass_input = trim($_POST['password'] ?? '');

    if ($admin_id > 0) {
        // --- EDIT / UPDATE LOGIC ---
        if (!empty($username)) {
            if (!empty($pass_input)) {
                $hashed_password = password_hash($pass_input, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssi", $username, $hashed_password, $admin_id);
            } else {
                $stmt = $conn->prepare("UPDATE admins SET username = ? WHERE id = ?");
                $stmt->bind_param("si", $username, $admin_id);
            }
            $stmt->execute();
            $stmt->close();
            header("Location: admin_list.php?msg=updated");
            exit();
        } else {
            $error_type = "fill";
            $message = "OHHH!!! MAAAMMMAAAMIAAA!!! GAME-OVER!! YOU LOST A LIFE FOR FILL UP THE ADMIN FIRSTTTT!!";
        }
    } else {
        // --- ADD NEW ADMIN LOGIC ---
        if (!empty($username) && !empty($pass_input)) {
            $hashed_password = password_hash($pass_input, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (username, password, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $username, $hashed_password);
            $stmt->execute();
            $stmt->close();
            header("Location: admin_list.php?msg=added");
            exit();
        } else {
            $error_type = "fill";
            $message = "OHHH!!! MAAAMMMAAAMIAAA!!! GAME-OVER!! YOU LOST A LIFE FOR FILL UP THE ADMIN FIRSTTTT!!";
        }
    }
}

// 4. PAGPROSESO SA PAGDELETE NG ADMIN
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_list.php?msg=deleted");
    exit();
}

// 5. KUHANIN ANG LAHAT NG ADMINS MULA SA DATABASE
$result = $conn->query("SELECT * FROM admins ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin List & Form - NSMBWii Style</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Comic Sans MS', 'Chalkboard SE', 'Fredoka One', cursive, sans-serif;
            background-image: url('../Mario Wallpaper/NSMBWii.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #000;
        }

        .main-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .top-nav {
            text-align: right;
            margin-bottom: 15px;
        }

        .top-nav a {
            color: #0000aa;
            text-decoration: underline;
            font-weight: bold;
            font-size: 18px;
            margin-left: 5px;
        }

        .top-nav a:hover {
            color: #ff0000;
        }

        .section-title {
            text-align: center;
            font-size: 26px;
            font-weight: 900;
            color: #ffcc00;
            text-shadow: 2px 2px 0px #000, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .title-underline {
            text-decoration: underline dashed #008000;
        }

        .form-box {
            background-color: #ffffff;
            border: 2px solid #0078d7;
            border-radius: 12px;
            padding: 20px 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .form-group label {
            width: 120px;
            font-weight: bold;
            font-size: 18px;
            text-align: right;
            margin-right: 15px;
        }

        .form-group input {
            width: 65%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            font-family: inherit;
        }

        .btn-submit-container {
            text-align: center;
            margin-top: 15px;
        }

        .btn-submit {
            background-color: #28a745;
            color: #ffffff;
            font-family: inherit;
            font-size: 18px;
            font-weight: bold;
            padding: 8px 25px;
            border: 2px solid #1e7e34;
            border-radius: 10px;
            cursor: pointer;
            text-shadow: 1px 1px 0px #000;
            box-shadow: 0 3px #155724;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .btn-cancel {
            display: inline-block;
            margin-left: 10px;
            background-color: #dc3545;
            color: #fff;
            padding: 8px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }

        .rainbow-line {
            height: 5px;
            background: linear-gradient(to right, #ff0000, #ff9900, #ffff00, #00ff00, #00ffff, #0000ff);
            border-radius: 3px;
            margin: 25px 0;
        }

        .admin-table-container {
            background-color: #ffffff;
            border: 2px solid #0078d7;
            border-radius: 2px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin-top: 15px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table td {
            border: 1px solid #0078d7;
            padding: 12px 15px;
            font-size: 17px;
            vertical-align: middle;
        }

        .col-id { width: 20%; }
        .col-username { width: 35%; }
        .col-date { width: 30%; }
        .col-action { width: 15%; text-align: center; white-space: nowrap; }

        .action-edit {
            color: #0066cc;
            font-weight: bold;
            text-decoration: none;
            margin-right: 8px;
        }

        .action-edit:hover {
            text-decoration: underline;
        }

        .action-delete {
            color: #cc0000;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .action-delete:hover {
            text-decoration: underline;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-box {
            background-color: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            border: 3px solid #0078d7;
            text-align: center;
            max-width: 380px;
            width: 90%;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
            font-family: inherit;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-modal-ok {
            background-color: #28a745; color: #fff; border: none; padding: 8px 24px;
            font-size: 16px; font-weight: bold; border-radius: 8px; cursor: pointer;
        }

        .btn-modal-cancel {
            background-color: #dc3545; color: #fff; border: none; padding: 8px 24px;
            font-size: 16px; font-weight: bold; border-radius: 8px; cursor: pointer;
        }
    </style>
</head>
<body>

<div class="main-container">

    <div class="top-nav">
        <a href="../index.php">&larr; Back to Dashboard</a> | 
        <a href="admin_list.php#add-admin-form">+ Add / Edit Admin</a>
    </div>

    <div id="add-admin-form" class="form-box">
        <div class="section-title">
            <?php echo isset($is_edit) && $is_edit ? '&darr; EDIT ADMIN FORM &darr;' : '&darr; ADD NEW ADMIN FORM &darr;'; ?>
        </div>

        <?php if (!empty($message)): ?>
            <?php if (!empty($error_type)): ?>
                <div style="background: rgba(198, 40, 40, 0.9); border: 2px solid #ffeb3b; color: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; text-align: left;">
                    <img src="../Funny Memes/luigi-Gameover in 64 bits.png" alt="Luigi Game Over" style="width: 50px; height: auto; flex-shrink: 0;" onerror="this.style.display='none'">
                    <div style="font-weight: bold; font-size: 13px; line-height: 1.4;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                </div>
            <?php else: ?>
                <div style="text-align:center; color:green; font-weight:bold; margin-bottom:12px;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="admin_list.php" method="POST">
            <input type="hidden" name="admin_id" value="<?php echo isset($edit_id) ? $edit_id : 0; ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo isset($edit_username) ? htmlspecialchars($edit_username) : ''; ?>" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" <?php echo isset($is_edit) && $is_edit ? 'placeholder="(Iwanang bakante kung ayaw palitan)"' : ''; ?>>
            </div>

            <div class="btn-submit-container">
                <button type="submit" name="save_admin" class="btn-submit">
                    <?php echo isset($is_edit) && $is_edit ? 'UPDATE THIS ADMIN NOW' : 'ADD THIS ADMIN NOW'; ?>
                </button>

                <?php if (isset($is_edit) && $is_edit): ?>
                    <a href="admin_list.php" class="btn-cancel">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="rainbow-line"></div>

    <div class="section-title title-underline">ADMIN LIST</div>

    <div class="admin-table-container">
        <table class="admin-table">
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php 
                    $no = 1; 
                    while ($row = $result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td class="col-id">Admin ID: <?php echo $no++; ?></td>
                            <td class="col-username">| Username: <?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="col-date">| Date Created: <?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td class="col-action">
                                <a href="admin_list.php?edit_id=<?php echo $row['id']; ?>#add-admin-form" class="action-edit">Edit</a>
                                <a href="#" class="action-delete" onclick="showDeleteModal('admin_list.php?delete_id=<?php echo $row['id']; ?>'); return false;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">Walang nakatalang admin sa database. Magdagdag ng bago gamit ang form sa itaas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<div id="customDeleteModal" class="modal-overlay">
    <div class="modal-box">
        <p style="font-size:18px; font-weight:bold; margin:0;">Sigurado ka bang gusto mong idelete ito?</p>
        <div class="modal-buttons">
            <button id="modalOkBtn" class="btn-modal-ok">OK</button>
            <button onclick="closeDeleteModal()" class="btn-modal-cancel">Cancel</button>
        </div>
    </div>
</div>

<script>
    let targetDeleteUrl = '';

    function showDeleteModal(url) {
        targetDeleteUrl = url;
        document.getElementById('customDeleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('customDeleteModal').style.display = 'none';
        targetDeleteUrl = '';
    }

    document.getElementById('modalOkBtn').addEventListener('click', function() {
        if (targetDeleteUrl) {
            window.location.href = targetDeleteUrl;
        }
    });
</script>

</body>
</html>