<?php
// admin/inventory_list.php

// 1. Database Connection
$host     = "localhost";
$user     = "root";
$password = "@RBmadis28";
$database = "Mario_Activity";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksyon nabigo: " . $conn->connect_error);
}

// Ilagay ito pagkatapos mag-connect sa database upang hindi mag-error si Optimus Prime
if (isset($conn)) {
    $conn->query("DELETE FROM products WHERE product_name = 'Optimus Prime'");
}

// Auto-create sa `inventory` table kung wala pa[cite: 40]
$conn->query("CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) DEFAULT '',
    subcategory VARCHAR(100) DEFAULT '',
    product_details TEXT,
    photo VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Seguraduhing merong `photo` column kung nauna nang nagawa ang table[cite: 40]
$check_column =$conn->query("SHOW COLUMNS FROM inventory LIKE 'photo'");
if ($check_column->num_rows == 0) {$conn->query("ALTER TABLE inventory ADD COLUMN photo VARCHAR(255) DEFAULT ''");
}

$message = '';$is_edit = false;
$edit_id = 0;
$edit_name = '';$edit_price = '';
$edit_category = '';$edit_subcategory = '';
$edit_details = '';$edit_photo = '';

// Kuhanin ang notification mula sa URL[cite: 40]
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added')$message = "Tagumpay na naidagdag ang bagong Inventory Item!";
    if ($_GET['msg'] === 'updated')$message = "Tagumpay na na-update ang Inventory Item!";
    if ($_GET['msg'] === 'deleted')$message = "Tagumpay na nabura ang Inventory Item!";
}

// 2. EDIT LOGIC (Kuhanin ang datos kung pinindot ang Edit)[cite: 40]
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $stmt =$conn->prepare("SELECT * FROM inventory WHERE id = ?");
    $stmt->bind_param("i", $edit_id);$stmt->execute();
    $res =$stmt->get_result();
    if ($item = $res->fetch_assoc()) {$is_edit = true;
        $edit_name =$item['product_name'] ?? '';
        $edit_price =$item['product_price'] ?? '';
        $edit_category =$item['category'] ?? '';
        $edit_subcategory =$item['subcategory'] ?? '';
        $edit_details =$item['product_details'] ?? '';
        $edit_photo =$item['photo'] ?? '';
    }
    $stmt->close();
}

// 3. PAGPROSESO SA PAG-ADD O PAG-UPDATE NG INVENTORY ITEM[cite: 40]
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_inventory'])) {
    $inv_id      = intval($_POST['inv_id'] ?? 0);
    $prod_name   = trim($_POST['product_name'] ?? '');
    $prod_price  = floatval($_POST['product_price'] ?? 0);
    $category    = trim($_POST['category'] ?? '');
    $subcategory = trim($_POST['subcategory'] ?? '');
    $details     = trim($_POST['product_details'] ?? '');
    $photo_path  =$_POST['old_photo'] ?? '';

    // Handle File Upload kung may inupload na larawan[cite: 40]
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {$file_tmp = $_FILES['photo']['tmp_name'];$file_name = basename($_FILES['photo']['name']);$upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $new_file_path = $upload_dir . time() . '_' . $file_name;
        if (move_uploaded_file($file_tmp,$new_file_path)) {
            $photo_path =$new_file_path;
        }
    }

    // Sinusuri kung kumpleto ang mga pangunahing field bago i-save[cite: 40]
    if (!empty($prod_name) && $prod_price > 0 && !empty($category) && !empty($subcategory) && !empty($details)) {
        if ($inv_id > 0) {
            // Update[cite: 40]
            $stmt =$conn->prepare("UPDATE inventory SET product_name = ?, product_price = ?, category = ?, subcategory = ?, product_details = ?, photo = ? WHERE id = ?");
            $stmt->bind_param("sdssssi", $prod_name, $prod_price,$category, $subcategory,$details, $photo_path,$inv_id);
            $stmt->execute();$stmt->close();
            header("Location: inventory_list.php?msg=updated");
            exit();
        } else {
            // Insert[cite: 40]
            $stmt =$conn->prepare("INSERT INTO inventory (product_name, product_price, category, subcategory, product_details, photo, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sdssss", $prod_name,$prod_price, $category,$subcategory, $details,$photo_path);
            $stmt->execute();$stmt->close();
            header("Location: inventory_list.php?msg=added");
            exit();
        }
    } else {
        // Luigi Game-Over Error Message[cite: 40]
        $message = "OHHH MAAAAMAAAMIAA!!! GAME-OVERR!! YOU LOST A 1UP-LIFE FOR NEGLIGENCE IN FILLING A PRODUCT LIST FIRSTT!!";
    }
}

// 4. DELETE LOGIC (Kasama ang pag-reset ng AUTO_INCREMENT kung blangko na ang table)[cite: 40]
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt =$conn->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();$stmt->close();

    // Suriin kung wala nang laman ang table para i-reset ang ID sa 1[cite: 40]
    $check_empty =$conn->query("SELECT COUNT(*) as total FROM inventory");
    $data_count =$check_empty->fetch_assoc();
    if ($data_count['total'] == 0) {$conn->query("ALTER TABLE inventory AUTO_INCREMENT = 1");
    }

    header("Location: inventory_list.php?msg=deleted");
    exit();
}

// 5. KUHANIN ANG LAHAT NG INVENTORY ITEMS[cite: 40]
$result =$conn->query("SELECT * FROM inventory ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Comic Sans MS', 'Chalkboard SE', 'Fredoka One', cursive, sans-serif;
            background-image: url('../Mario Wallpaper/Power-up Panels.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #000;
        }

        .main-container {
            max-width: 950px;
            margin: 0 auto;
        }

        /* Top Navigation - Nakapwesto sa Kanang Bahagi (Right Side)[cite: 40] */
        .top-nav {
            text-align: right;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .top-nav a {
            color: #0056b3;
            text-decoration: underline;
            margin-left: 10px;
            text-shadow: 1px 1px 0px #fff;
        }

        .top-nav a:hover {
            color: #003366;
        }

        .form-box {
            background-color: rgba(255, 255, 255, 0.95);
            border: 2px solid #0078d7;
            border-radius: 12px;
            padding: 20px 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 25px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilo ng Pamagat: Dilaw sa loob, itim ang labas (outline)[cite: 40] */
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

        .form-group input, .form-group select, .form-group textarea {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #777;
            border-radius: 4px;
            font-size: 15px;
            font-family: inherit;
        }

        .currency-label {
            margin-right: 8px;
            font-weight: bold;
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

        /* Luigi Game-Over Alert Box Style[cite: 40] */
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
            font-size: 14px;
            text-shadow: 1px 1px 0px #fff;
        }

        /* Inventory List Design[cite: 40] */
        .inventory-title-container {
            margin-bottom: 5px;
        }

        .green-dashed-line {
            border-bottom: 2px dashed #28a745;
            margin-bottom: 15px;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border: 2px solid #0078d7;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .inventory-table tr {
            border-bottom: 2px solid #0078d7;
        }

        .inventory-table tr:last-child {
            border-bottom: none;
        }

        .inventory-table td {
            border: 1px solid #0078d7;
            padding: 12px 15px;
            font-size: 15px;
            vertical-align: middle;
            background-color: #ffffff;
            color: #000;
        }

        .col-id { width: 18%; font-weight: bold; }
        .col-name { width: 32%; }
        .col-price { width: 22%; }
        .col-date { width: 20%; font-size: 13px; }
        .col-action { width: 8%; text-align: center; white-space: nowrap; }

        .action-edit {
            color: #0056b3;
            font-weight: bold;
            text-decoration: none;
            margin-right: 5px;
        }
        .action-edit:hover { text-decoration: underline; }

        .action-delete {
            color: #cc0000;
            font-weight: bold;
            text-decoration: none;
        }
        .action-delete:hover { text-decoration: underline; }

        .item-photo-img {
            width: 35px;
            height: 35px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ccc;
            vertical-align: middle;
            margin-left: 5px;
        }
    </style>
</head>
<body>

<div class="main-container">

    <!-- Top Navigation (Naka-align sa Right Side)[cite: 40] -->
    <div class="top-nav">
        <a href="../index.php">&larr; Back to Dashboard</a> | 
        <a href="inventory_list.php">+ Add New Inventory Item</a>
    </div>

    <!-- ADD / EDIT INVENTORY FORM[cite: 40] -->
    <div class="form-box">
        <div class="yellow-black-title">
            <?php echo $is_edit ? '↓ EDIT INVENTORY ITEM FORM ↓' : '↓ ADD NEW INVENTORY ITEM FORM ↓'; ?>
        </div>

        <?php if (!empty($message)): ?>
            <?php if (strpos($message, 'GAME-OVERR') !== false): ?>
                <!-- Luigi Game-Over Error Box[cite: 40] -->
                <div class="luigi-alert-box">
                    <img src="../Funny Memes/Luigi Advance 4 Game-over.png" alt="Luigi Game-Over" onerror="this.style.display='none'">
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php else: ?>
                <!-- Success Message[cite: 40] -->
                <div style="text-align:center; color:green; font-weight:bold; margin-bottom:15px;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="inventory_list.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="inv_id" value="<?php echo $edit_id; ?>">
            <input type="hidden" name="old_photo" value="<?php echo htmlspecialchars($edit_photo); ?>">

            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($edit_name); ?>" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="product_price">Product Price</label>
                <div style="display: flex; align-items: center; flex: 1;">
                    <span class="currency-label">Php</span>
                    <input type="number" step="0.01" id="product_price" name="product_price" value="<?php echo htmlspecialchars($edit_price); ?>" autocomplete="off" style="flex: 1;">
                </div>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">-- Pumili ng Category --</option>
                    <option value="Transformers" <?php if($edit_category=='Transformers') echo 'selected'; ?>>Transformers</option>
                    <option value="Gundam" <?php if($edit_category=='Gundam') echo 'selected'; ?>>Gundam</option>
                    <option value="Call of Duty" <?php if($edit_category=='Call of Duty') echo 'selected'; ?>>Call of Duty</option>
                    <option value="Valorant" <?php if($edit_category=='Valorant') echo 'selected'; ?>>Valorant</option>
                    <option value="Mobile Legends" <?php if($edit_category=='Mobile Legends') echo 'selected'; ?>>Mobile Legends</option>
                    <option value="Naruto" <?php if($edit_category=='Naruto') echo 'selected'; ?>>Naruto</option>
                    <option value="Code Geass" <?php if($edit_category=='Code Geass') echo 'selected'; ?>>Code Geass</option>
                </select>
            </div>

            <div class="form-group">
                <label for="subcategory">Subcategory</label>
                <select id="subcategory" name="subcategory">
                    <option value="">-- Pumili ng Subcategory --</option>
                    <option value="Figures" <?php if($edit_subcategory=='Figures') echo 'selected'; ?>>Figures</option>
                    <option value="Manga" <?php if($edit_subcategory=='Manga') echo 'selected'; ?>>Manga</option>
                    <option value="Dolls" <?php if($edit_subcategory=='Dolls') echo 'selected'; ?>>Dolls</option>
                    <option value="Keychain" <?php if($edit_subcategory=='Keychain') echo 'selected'; ?>>Keychain</option>
                    <option value="Card Game" <?php if($edit_subcategory=='Card Game') echo 'selected'; ?>>Card Game</option>
                    <option value="Tees" <?php if($edit_subcategory=='Tees') echo 'selected'; ?>>Tees</option>
                </select>
            </div>

            <div class="form-group">
                <label for="product_details">Product Details</label>
                <textarea id="product_details" name="product_details" rows="3"><?php echo htmlspecialchars($edit_details); ?></textarea>
            </div>

            <div class="form-group">
                <label for="photo">Product Image</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>

            <?php if (!empty($edit_photo) && file_exists($edit_photo)): ?>
                <div style="text-align: center; margin-bottom: 15px;">
                    <span style="font-size: 14px; font-weight: bold;">Current Image:</span><br>
                    <img src="<?php echo htmlspecialchars($edit_photo); ?>" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #777; margin-top: 5px;">
                </div>
            <?php endif; ?>

            <div class="btn-submit-container">
                <button type="submit" name="save_inventory" class="btn-create">
                    <?php echo $is_edit ? 'UPDATE THIS ITEM NOW' : 'ADD THIS ITEM NOW'; ?>
                </button>
                <?php if ($is_edit): ?>
                    <a href="inventory_list.php" style="margin-left: 10px; font-weight: bold; color: #cc0000; text-decoration: none;">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- INVENTORY LIST SECTION[cite: 40] -->
    <div class="inventory-title-container">
        <div class="yellow-black-title" style="text-align: left; display: inline-block;">INVENTORY LIST</div>
        <div class="green-dashed-line"></div>
    </div>

    <table class="inventory-table">
        <tbody>
            <?php if ($result &&$result->num_rows > 0): ?>
                <?php while ($row =$result->fetch_assoc()): ?>
                    <?php 
                        $row_photo =$row['photo'] ?? '';
                        $formatted_price = number_format($row['product_price'], 2);
                    ?>
                    <tr>
                        <td class="col-id">Product ID:<br><strong><?php echo $row['id']; ?></strong></td>
                        <td class="col-name">
                            | <strong><?php echo htmlspecialchars($row['product_name']); ?></strong>
                            <?php if (!empty($row_photo) && file_exists($row_photo)): ?>
                                <img src="<?php echo htmlspecialchars($row_photo); ?>" alt="Item" class="item-photo-img">
                            <?php endif; ?>
                        </td>
                        <td class="col-price">Price: Php<br><strong><?php echo $formatted_price; ?></strong></td>
                        <td class="col-date">| Date Added: <br><?php echo $row['created_at']; ?></td>
                        <td class="col-action">
                            <a href="inventory_list.php?edit_id=<?php echo $row['id']; ?>" class="action-edit">Edit</a><br>
                            <a href="inventory_list.php?delete_id=<?php echo $row['id']; ?>" class="action-delete" onclick="return confirm('Sigurado ka bang gusto mong burahin ang item na ito?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Walang nakatalang inventory item sa database.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

</body>
</html>