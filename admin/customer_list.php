<?php
// admin/customer_list.php

// 1. Database Connection
$host     = "localhost";
$user     = "root";
$password = "@RBmadis28";
$database = "Mario_Activity";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksyon nabigo: " . $conn->connect_error);
}

// Auto-create sa `customers` table kung wala pa
$conn->query("CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT '',
    address TEXT NOT NULL,
    profile_image VARCHAR(255) DEFAULT '',
    username VARCHAR(255) DEFAULT '',
    password VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Seguraduhing merong tamang columns kung nauna nang nagawa ang table
$check_col_email =$conn->query("SHOW COLUMNS FROM customers LIKE 'email'");
if ($check_col_email->num_rows == 0) {$conn->query("ALTER TABLE customers ADD COLUMN email VARCHAR(255) DEFAULT ''");
}
$check_col_img =$conn->query("SHOW COLUMNS FROM customers LIKE 'profile_image'");
if ($check_col_img->num_rows == 0) {$conn->query("ALTER TABLE customers ADD COLUMN profile_image VARCHAR(255) DEFAULT ''");
}
$check_col_uname =$conn->query("SHOW COLUMNS FROM customers LIKE 'username'");
if ($check_col_uname->num_rows == 0) {$conn->query("ALTER TABLE customers ADD COLUMN username VARCHAR(255) DEFAULT ''");
}
$check_col_pass =$conn->query("SHOW COLUMNS FROM customers LIKE 'password'");
if ($check_col_pass->num_rows == 0) {$conn->query("ALTER TABLE customers ADD COLUMN password VARCHAR(255) DEFAULT ''");
}

// Awtomatikong i-sync ang mga customer mula sa users table patungo sa customers table
$sync_check =$conn->query("SELECT COUNT(*) as total FROM customers");
if ($sync_check) {
    $sync_data =$sync_check->fetch_assoc();
    if ($sync_data['total'] == 0) {$conn->query("INSERT INTO customers (name, email, address, profile_image, username, password, created_at) SELECT username, email, '', '', username, password, date_added FROM users WHERE role = 'Customer'");
    }
}

$message = '';$is_edit = false;
$edit_id = 0;
$edit_name = '';$edit_email = '';
$edit_address = '';$edit_profile_image = '';
$edit_username = '';$edit_password = '';

// Kuhanin ang notification mula sa URL
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added')$message = "Tagumpay na naidagdag ang bagong Customer!";
    if ($_GET['msg'] === 'updated')$message = "Tagumpay na na-update ang Customer!";
    if ($_GET['msg'] === 'deleted')$message = "Tagumpay na nabura ang Customer!";
}

// 2. EDIT LOGIC
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $stmt =$conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $edit_id);$stmt->execute();
    $res =$stmt->get_result();
    if ($item = $res->fetch_assoc()) {$is_edit = true;
        $edit_name =$item['name'] ?? '';
        $edit_email =$item['email'] ?? '';
        $edit_address =$item['address'] ?? '';
        $edit_profile_image =$item['profile_image'] ?? '';
        $edit_username =$item['username'] ?? '';
        $edit_password =$item['password'] ?? '';
    }
    $stmt->close();
}

// 3. PAGPROSESO SA PAG-ADD O PAG-UPDATE NG CUSTOMER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_customer'])) {
    $cust_id     = intval($_POST['cust_id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $username    = trim($_POST['username'] ?? '');
    $pass_input  = trim($_POST['password'] ?? '');
    $image_path  =$_POST['old_profile_image'] ?? '';

    // Handle File Upload kung may inupload na profile image
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {$file_name = basename($_FILES['profile_image']['name']);$upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $new_file_path = $upload_dir . time() . '_' . $file_name;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'],$new_file_path)) {
            $image_path =$new_file_path;
        }
    }

    if (!empty($name) && !empty($username)) {
        if ($cust_id > 0) {
            // Update
            if (!empty($pass_input)) {
                $hashed_pass = password_hash($pass_input, PASSWORD_DEFAULT);
                $stmt =$conn->prepare("UPDATE customers SET name = ?, email = ?, address = ?, profile_image = ?, username = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssssssi", $name, $email,$address, $image_path,$username, $hashed_pass,$cust_id);
            } else {
                $stmt =$conn->prepare("UPDATE customers SET name = ?, email = ?, address = ?, profile_image = ?, username = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $name,$email, $address,$image_path, $username,$cust_id);
            }
            $stmt->execute();$stmt->close();
            header("Location: customer_list.php?msg=updated");
            exit();
        } else {
            // Insert
            $hashed_pass = !empty($pass_input) ? password_hash($pass_input, PASSWORD_DEFAULT) : '';
            $stmt =$conn->prepare("INSERT INTO customers (name, email, address, profile_image, username, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssss", $name,$email, $address,$image_path, $username,$hashed_pass);
            $stmt->execute();$stmt->close();
            header("Location: customer_list.php?msg=added");
            exit();
        }
    } else {
        $message = "OHHH MAAAAMAAAMIAA!!! GAME-OVERR!! YOU LOST A 1UP-LIFE FOR NEGLIGENCE IN FILLING A CUSTOMER FIELDS FIRSTT!!";
    }
}

// 4. DELETE LOGIC
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt =$conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();$stmt->close();

    $check_empty =$conn->query("SELECT COUNT(*) as total FROM customers");
    $data_count =$check_empty->fetch_assoc();
    if ($data_count['total'] == 0) {$conn->query("ALTER TABLE customers AUTO_INCREMENT = 1");
    }

    header("Location: customer_list.php?msg=deleted");
    exit();
}

// 5. KUHANIN ANG LAHAT NG CUSTOMERS
$result =$conn->query("SELECT * FROM customers ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Comic Sans MS', 'Chalkboard SE', 'Fredoka One', cursive, sans-serif;
            background-image: url('../Mario Wallpaper/NSMBWii2.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #000;
        }

        .main-container {
            max-width: 1050px;
            margin: 0 auto;
        }

        .top-nav {
            text-align: right;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .top-nav a {
            color: #ffffff;
            text-decoration: underline;
            margin-left: 10px;
            text-shadow: 2px 2px 0px #000, -1px -1px 0px #000, 1px -1px 0px #000, -1px 1px 0px #000;
            font-size: 16px;
        }

        .top-nav a:hover {
            color: #ffeb3b;
        }

        .table-box {
            background-color: rgba(255, 255, 255, 0.95);
            border: 2px solid #0078d7;
            border-radius: 10px;
            padding: 20px 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 25px;
        }

        .yellow-black-title {
            text-align: center;
            font-size: 26px;
            font-weight: 900;
            color: #ffeb3b;
            text-shadow: 
                -1px -1px 0 #000,  
                 1px -1px 0 #000,
                -1px  1px 0 #000,
                 1px  1px 0 #000,
                 2px  2px 0px rgba(0,0,0,0.5);
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            width: 140px;
            font-weight: bold;
            font-size: 15px;
        }

        .form-group input, .form-group textarea {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #777;
            border-radius: 4px;
            font-size: 15px;
            font-family: inherit;
        }

        .btn-submit-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-create {
            background-color: #28a745;
            color: #fff;
            font-family: inherit;
            font-size: 16px;
            font-weight: bold;
            padding: 8px 25px;
            border: 2px solid #1e7e34;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-create:hover {
            background-color: #218838;
        }

        .luigi-alert-box {
            background-color: #ffebee;
            border: 2px dashed #cc0000;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .luigi-alert-box img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .luigi-alert-box span {
            color: #cc0000;
            font-weight: bold;
            font-size: 13px;
            text-shadow: 1px 1px 0px #fff;
        }

        .customer-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border: 2px solid #0078d7;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .customer-table tr {
            border-bottom: 2px solid #0078d7;
        }

        .customer-table tr:last-child {
            border-bottom: none;
        }

        .customer-table td {
            border: 1px solid #0078d7;
            padding: 10px 12px;
            font-size: 14px;
            vertical-align: middle;
            background-color: #ffffff;
            color: #000;
        }

        .col-id { width: 8%; font-weight: bold; }
        .col-name { width: 18%; }
        .col-uname { width: 15%; }
        .col-email { width: 18%; }
        .col-address { width: 16% }
        .col-date { width: 15%; font-size: 12px; }
        .col-action { width: 10%; text-align: center; white-space: nowrap; }

        .action-edit {
            color: #0056b3;
            font-weight: bold;
            text-decoration: none;
            margin-right: 3px;
        }
        .action-edit:hover { text-decoration: underline; }

        .action-delete {
            color: #cc0000;
            font-weight: bold;
            text-decoration: none;
        }
        .action-delete:hover { text-decoration: underline; }

        .item-photo-img {
            width: 30px;
            height: 30px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ccc;
            vertical-align: middle;
            margin-left: 4px;
        }
    </style>
</head>
<body>

<div class="main-container">

    <div class="top-nav">
        <a href="../index.php">&larr; Back to Dashboard</a> | 
        <a href="customer_list.php">+ Add New Customer</a>
    </div>

    <div class="table-box">
        <div class="yellow-black-title">
            <?php echo $is_edit ? '↓ EDIT CUSTOMER FORM ↓' : '↓ ADD NEW CUSTOMER FORM ↓'; ?>
        </div>

        <?php if (!empty($message)): ?>
            <?php if (strpos($message, 'GAME-OVERR') !== false): ?>
                <div class="luigi-alert-box">
                    <img src="../Funny Memes/Luigi Advance 4 Game-over.png" alt="Luigi Game-Over" onerror="this.style.display='none'">
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php else: ?>
                <div style="text-align:center; color:green; font-weight:bold; margin-bottom:15px;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="customer_list.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="cust_id" value="<?php echo $edit_id; ?>">
            <input type="hidden" name="old_profile_image" value="<?php echo htmlspecialchars($edit_profile_image); ?>">

            <div class="form-group">
                <label for="name">Pangalan</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($edit_name); ?>" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($edit_email); ?>" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($edit_address); ?>" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_username); ?>" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="<?php echo $is_edit ? '(Iwanang bakante kung ayaw palitan)' : ''; ?>">
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>

            <?php if (!empty($edit_profile_image) && file_exists($edit_profile_image)): ?>
                <div style="text-align: center; margin-bottom: 15px;">
                    <span style="font-size: 14px; font-weight: bold;">Current Image:</span><br>
                    <img src="<?php echo htmlspecialchars($edit_profile_image); ?>" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #777; margin-top: 5px;">
                </div>
            <?php endif; ?>

            <div class="btn-submit-container">
                <button type="submit" name="save_customer" class="btn-create">
                    <?php echo $is_edit ? 'UPDATE CUSTOMER NOW' : 'CREATE CUSTOMER NOW'; ?>
                </button>
                <?php if ($is_edit): ?>
                    <a href="customer_list.php" style="margin-left: 10px; font-weight: bold; color: #cc0000; text-decoration: none;">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-box">
        <div class="yellow-black-title" style="text-align: left; margin-bottom: 15px;">Customer list</div>
        
        <table class="customer-table">
            <tbody>
                <?php if ($result &&$result->num_rows > 0): ?>
                    <?php while ($row =$result->fetch_assoc()): ?>
                        <?php 
                            $cust_photo =$row['profile_image'] ?? '';
                            $display_username = !empty($row['username']) ? $row['username'] :$row['name'];
                        ?>
                        <tr>
                            <td class="col-id">ID: <?php echo $row['id']; ?></td>
                            <td class="col-name">Name: <?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="col-uname">
                                User: <strong><?php echo htmlspecialchars($display_username); ?></strong>
                                <?php if (!empty($cust_photo) && file_exists($cust_photo)): ?>
                                    <img src="<?php echo htmlspecialchars($cust_photo); ?>" alt="Profile" class="item-photo-img">
                                <?php endif; ?>
                            </td>
                            <td class="col-email">Email: <?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="col-address">Addr: <?php echo htmlspecialchars($row['address']); ?></td>
                            <td class="col-date">Date: <?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td class="col-action">
                                <a href="customer_list.php?edit_id=<?php echo $row['id']; ?>" class="action-edit">Edit</a> | 
                                <a href="customer_list.php?delete_id=<?php echo $row['id']; ?>" class="action-delete" onclick="return confirm('Sigurado ka bang gusto mong burahin ang customer na ito?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">Walang nakatalang customer sa database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>