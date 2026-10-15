<?php
// products.php - Raizeness Inventory Anime System
require_once 'Model/DB_Model.php';

if (!isset($connection) && isset($conn)) {
    $connection = $conn;
}

if (isset($connection)) {
    // Alisin si Optimus Prime para hindi mag-error
    $connection->query("DELETE FROM products WHERE product_name = 'Optimus Prime'");

    // Siguraduhing umiiral ang table
    $connection->query("CREATE TABLE IF NOT EXISTS `products` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `product_name` varchar(255) NOT NULL,
      `price` decimal(10,2) NOT NULL,
      `details` text NOT NULL,
      `category` varchar(100) NOT NULL,
      `subcategory` varchar(100) NOT NULL,
      `date_added` datetime NOT NULL,
      `product_image` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Idagdag ang product_image column kung wala pa sa lumang table
    $check_col = $connection->query("SHOW COLUMNS FROM products LIKE 'product_image'");
    if ($check_col && $check_col->num_rows == 0) {
        $connection->query("ALTER TABLE products ADD COLUMN product_image VARCHAR(255) DEFAULT NULL");
    }

    // Magdagdag ng 2 sample items kung blangko ang table
    $check_empty = $connection->query("SELECT COUNT(*) as total FROM products");
    if ($check_empty) {
        $data_count = $check_empty->fetch_assoc();
        if ($data_count['total'] == 0) {
            $connection->query("INSERT INTO products (product_name, price, details, category, subcategory, date_added, product_image) VALUES 
            ('Sage Chibi Cute and Sexy Doll Healer Pose', 100.00, 'Healer Pose Doll', 'Valorant', 'Dolls', '2026-10-03 20:55:59', ''),
            ('C.C Sexy Style of Doll Figure', 120.00, 'Doll Figure Style', 'Code Geass', 'Figures', '2026-10-03 20:57:11', '')");
        }
    }
}

$msg = "";
$error_msg = "";

// PROSESO NG PAGDARAGDAG NG PRODUKTO
if (isset($_POST['add_product']) && isset($connection)) {
    $p_name = trim($_POST['product_name'] ?? '');
    $p_price = trim($_POST['price'] ?? '');
    $p_details = trim($_POST['details'] ?? '');
    $p_cat = trim($_POST['category'] ?? '');
    $p_subcat = trim($_POST['subcategory'] ?? '');

    // I-check kung may blangko sa fields para lumabas ang Game-Over alert
    if (empty($p_name) || empty($p_price) || empty($p_details) || empty($p_cat) || empty($p_subcat)) {
        $error_msg = "OHHH!! MAAMMAAMIAA!! GAME-OVER! YOU LOST A 1-UP Life FOR FILLING ALL THE FIELDS FIRSST!!";
    } else {
        $date_added = date('Y-m-d H:i:s');
        $img_name = $_FILES['product_image']['name'];
        $img_tmp = $_FILES['product_image']['tmp_name'];
        
        $folder = "Gundam Images/";
        if ($p_cat == 'Transformers') {
            $folder = "Transformers Images/";
        } elseif ($p_cat == 'Valorant' || $p_cat == 'Valorants') {
            $folder = "Valorants/";
        } elseif ($p_cat == 'Code Geass') {
            $folder = "Code Geass/";
        } elseif ($p_cat == 'Mario Meme') {$folder = "Mario Meme/";
        }
        
        $target_file = "";
        if (!empty($img_name)) {$target_file = $folder . basename($img_name);
            move_uploaded_file($img_tmp,$target_file);
        }

        $stmt =$connection->prepare("INSERT INTO products (product_name, price, details, category, subcategory, date_added, product_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsssss", $p_name, $p_price,$p_details, $p_cat,$p_subcat, $date_added,$target_file);
        if ($stmt->execute()) {$msg = "Tagumpay na naidagdag ang produkto!";
        } else {
            $error_msg = "May error sa pagdagdag ng produkto.";
        }
        $stmt->close();
    }
}

// PROSESO NG EDIT / UPDATE NG PRODUKTO
if (isset($_POST['update_product']) && isset($connection)) {
    $u_id = intval($_POST['id']);
    $p_name = trim($_POST['product_name'] ?? '');
    $p_price = trim($_POST['price'] ?? '');
    $p_details = trim($_POST['details'] ?? '');
    $p_cat = trim($_POST['category'] ?? '');
    $p_subcat = trim($_POST['subcategory'] ?? '');

    if (empty($p_name) || empty($p_price) || empty($p_details) || empty($p_cat) || empty($p_subcat)) {$error_msg = "OHHH!! MAAMMAAMIAA!! GAME-OVER! YOU LOST A 1-UP Life FOR FILLING ALL THE FIELDS FIRSST!!";
    } else {
        $img_name =$_FILES['product_image']['name'];
        $img_tmp =$_FILES['product_image']['tmp_name'];

        if (!empty($img_name)) {$folder = "Gundam Images/";
            if ($p_cat == 'Transformers') {$folder = "Transformers Images/";
            } elseif ($p_cat == 'Valorant' || $p_cat == 'Valorants') {$folder = "Valorants/";
            } elseif ($p_cat == 'Code Geass') {$folder = "Code Geass/";
            } elseif ($p_cat == 'Mario Meme') {$folder = "Mario Meme/";
            }
            $target_file = $folder . basename($img_name);
            move_uploaded_file($img_tmp,$target_file);

            $stmt =$connection->prepare("UPDATE products SET product_name=?, price=?, details=?, category=?, subcategory=?, product_image=? WHERE id=?");
            $stmt->bind_param("sdssssi", $p_name, $p_price,$p_details, $p_cat,$p_subcat, $target_file,$u_id);
        } else {
            $stmt =$connection->prepare("UPDATE products SET product_name=?, price=?, details=?, category=?, subcategory=? WHERE id=?");
            $stmt->bind_param("sdsssi", $p_name,$p_price, $p_details,$p_cat, $p_subcat,$u_id);
        }
        
        $stmt->execute();$stmt->close();
        echo "<script>window.location.href='products.php';</script>";
        exit();
    }
}

// PROSESO NG DELETE NG PRODUKTO
if (isset($_GET['delete_id']) && isset($connection)) {
    $del_id = intval($_GET['delete_id']);
    $stmt =$connection->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();$stmt->close();
    echo "<script>window.location.href='products.php';</script>";
    exit();
}
?>
<html>
<head>
<title>Products List - Raizeness Inventory Anime System</title>
<style>
    body {
        background-image: url('Mario Wallpaper/Mario Galaxy Movie.jpg');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
        font-family: "Comic Sans MS", "Chalkboard SE", sans-serif;
        color: #000;
        margin: 0;
        padding: 0;
    }
    #mainWrapper {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 30px;
        margin: 4vh auto;
        width: 90%;
        max-width: 1100px;
        border: 4px solid #000;
        box-shadow: 0px 8px 16px rgba(0,0,0,0.3);
    }
    h2 {
        color: #d32f2f;
        text-shadow: 2px 2px #ffeb3b;
        text-align: center;
    }
    .nav-back, .btn-add {
        display: inline-block;
        margin-bottom: 20px;
        color: #1976d2;
        text-decoration: none;
        font-weight: bold;
        padding: 8px 15px;
        border: 2px solid #1976d2;
        border-radius: 10px;
        background: #e3f2fd;
        cursor: pointer;
    }
    .btn-add {
        background: #e8f5e9;
        color: #388e3c;
        border-color: #388e3c;
    }
    .form-container {
        background: #fff5f5;
        border: 2px dashed #d32f2f;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: none;
    }
    .form-container input, .form-container select, .form-container textarea {
        width: 100%;
        padding: 8px;
        margin: 5px 0 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #ffb300;
        color: #fff;
        text-shadow: 1px 1px #333;
    }
    tr:hover {
        background-color: #f9f9f9;
    }
    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        font-weight: bold;
        color: white;
        display: inline-block;
        margin: 2px;
    }
    .btn-edit { background: #1976d2; }
    .btn-delete { background: #f44336; }

    /* Luigi Game-Over Alert Box Style */
    .gameover-alert {
        background-color: rgba(198, 40, 40, 0.95);
        border: 2px dashed #ffeb3b;
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        text-align: left;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .gameover-alert img {
        width: 55px;
        height: auto;
        flex-shrink: 0;
    }
    .gameover-alert span {
        font-weight: bold;
        font-size: 14px;
        line-height: 1.4;
        text-shadow: 1px 1px #000;
    }
</style>
<script>
    function toggleAddForm() {
        var form = document.getElementById('addFormContainer');
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
    }
</script>
</head>
<body>

<div id="mainWrapper">
    <h2>🍄 Raizeness Inventory - Product List 🍄</h2>
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="index.php?page=home" class="nav-back">🏠 Bumalik sa Home</a>
        <button onclick="toggleAddForm()" class="btn-add">➕ Magdagdag ng Produkto</button>
    </div>

    <?php if (!empty($msg)): ?>
        <p style='color: green; font-weight: bold; text-align: center;'><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="gameover-alert">
            <img src="Funny Memes/mario-gameover-pic-in-64bits.png" alt="Game Over" onerror="this.style.display='none'">
            <span><?php echo htmlspecialchars($error_msg); ?></span>
        </div>
    <?php endif; ?>

    <!-- ADD PRODUCT FORM -->
    <div id="addFormContainer" class="form-container" style="<?php echo (!empty($error_msg) && !isset($_POST['update_product'])) ? 'display: block;' : ''; ?>">
        <h3>Magdagdag ng Bagong Produkto</h3>
        <form action="products.php" method="POST" enctype="multipart/form-data">
            <label>Pangalan ng Produkto:</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($_POST['product_name'] ?? ''); ?>">
            
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="">-- Pumili ng Category --</option>
                <option value="Transformers" <?php if(isset($_POST['category']) &&$_POST['category']=='Transformers') echo 'selected'; ?>>Transformers</option>
                <option value="Gundam" <?php if(isset($_POST['category']) &&$_POST['category']=='Gundam') echo 'selected'; ?>>Gundam</option>
                <option value="Call of Duty" <?php if(isset($_POST['category']) &&$_POST['category']=='Call of Duty') echo 'selected'; ?>>Call of Duty</option>
                <option value="Valorant" <?php if(isset($_POST['category']) &&$_POST['category']=='Valorant') echo 'selected'; ?>>Valorant</option>
                <option value="Mobile Legends" <?php if(isset($_POST['category']) &&$_POST['category']=='Mobile Legends') echo 'selected'; ?>>Mobile Legends</option>
                <option value="Naruto" <?php if(isset($_POST['category']) &&$_POST['category']=='Naruto') echo 'selected'; ?>>Naruto</option>
                <option value="Code Geass" <?php if(isset($_POST['category']) &&$_POST['category']=='Code Geass') echo 'selected'; ?>>Code Geass</option>
            </select>
            
            <label for="subcategory">Subcategory:</label>
            <select id="subcategory" name="subcategory">
                <option value="">-- Pumili ng Subcategory --</option>
                <option value="Figures" <?php if(isset($_POST['subcategory']) &&$_POST['subcategory']=='Figures') echo 'selected'; ?>>Figures</option>
                <option value="Manga" <?php if(isset($_POST['subcategory']) &&$_POST['subcategory']=='Manga') echo 'selected'; ?>>Manga</option>
                <option value="Dolls" <?php if(isset($_POST['subcategory']) &&$_POST['subcategory']=='Dolls') echo 'selected'; ?>>Dolls</option>
                <option value="Keychain" <?php if(isset($_POST['subcategory']) &&$_POST['subcategory']=='Keychain') echo 'selected'; ?>>Keychain</option>
                <option value="Card Game" <?php if(isset($_POST['subcategory']) &&$_POST['subcategory']=='Card Game') echo 'selected'; ?>>Card Game</option>
                <option value="Tees" <?php if(isset($_POST['subcategory']) &&$_POST['subcategory']=='Tees') echo 'selected'; ?>>Tees</option>
            </select>
            
            <label>Presyo (₱):</label>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
            
            <label>Detalye:</label>
            <textarea name="details" rows="2"><?php echo htmlspecialchars($_POST['details'] ?? ''); ?></textarea>
            
            <label>Larawan ng Produkto:</label>
            <input type="file" name="product_image" accept="image/*">
            
            <button type="submit" name="add_product" style="background: #388e3c; color: white; padding: 8px 15px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; width: 100%;">I-save ang Produkto</button>
        </form>
    </div>

    <!-- EDIT PRODUCT FORM -->
    <?php
    if (isset($_GET['edit_id']) && isset($connection)) {
        $edit_id = intval($_GET['edit_id']);
        $edit_res = mysqli_query($connection, "SELECT * FROM products WHERE id = $edit_id");
        if ($edit_row = mysqli_fetch_assoc($edit_res)) {
            $edit_category =$edit_row['category'];
            $edit_subcategory =$edit_row['subcategory'];
    ?>
    <div class="form-container" style="display: block; background: #e3f2fd; border: 2px dashed #1976d2;">
        <h3 style="color: #1976d2;">I-edit ang Produkto</h3>
        <form action="products.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">
            
            <label>Pangalan ng Produkto:</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($edit_row['product_name']); ?>">
            
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="Transformers" <?php if($edit_category=='Transformers') echo 'selected'; ?>>Transformers</option>
                <option value="Gundam" <?php if($edit_category=='Gundam') echo 'selected'; ?>>Gundam</option>
                <option value="Call of Duty" <?php if($edit_category=='Call of Duty') echo 'selected'; ?>>Call of Duty</option>
                <option value="Valorant" <?php if($edit_category=='Valorant') echo 'selected'; ?>>Valorant</option>
                <option value="Mobile Legends" <?php if($edit_category=='Mobile Legends') echo 'selected'; ?>>Mobile Legends</option>
                <option value="Naruto" <?php if($edit_category=='Naruto') echo 'selected'; ?>>Naruto</option>
                <option value="Code Geass" <?php if($edit_category=='Code Geass') echo 'selected'; ?>>Code Geass</option>
                <option value="Mario Meme" <?php if($edit_category=='Mario Meme') echo 'selected'; ?>>Mario Meme</option>
            </select>
            
            <label for="subcategory">Subcategory:</label>
            <select id="subcategory" name="subcategory">
                <option value="Figures" <?php if($edit_subcategory=='Figures') echo 'selected'; ?>>Figures</option>
                <option value="Manga" <?php if($edit_subcategory=='Manga') echo 'selected'; ?>>Manga</option>
                <option value="Dolls" <?php if($edit_subcategory=='Dolls') echo 'selected'; ?>>Dolls</option>
                <option value="Keychain" <?php if($edit_subcategory=='Keychain') echo 'selected'; ?>>Keychain</option>
                <option value="Card Game" <?php if($edit_subcategory=='Card Game') echo 'selected'; ?>>Card Game</option>
                <option value="Tees" <?php if($edit_subcategory=='Tees') echo 'selected'; ?>>Tees</option>
            </select>
            
            <label>Presyo (₱):</label>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($edit_row['price']); ?>">
            
            <label>Detalye:</label>
            <textarea name="details" rows="2"><?php echo htmlspecialchars($edit_row['details']); ?></textarea>
            
            <label>Palitan ang Larawan (Optional):</label>
            <input type="file" name="product_image" accept="image/*">
            
            <button type="submit" name="update_product" style="background: #1976d2; color: white; padding: 8px 15px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">I-update ang Produkto</button>
            <a href="products.php" style="margin-left: 10px; text-decoration: none; color: #555; font-weight: bold;">Kanselahin</a>
        </form>
    </div>
    <?php 
        }
    }
    ?>

    <!-- PRODUCTS TABLE -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Larawan</th>
                <th>Pangalan ng Produkto</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Presyo</th>
                <th>Detalye</th>
                <th>Aksyon</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($connection)) {
                $result = mysqli_query($connection, "SELECT * FROM products ORDER BY id ASC");
                if ($result && mysqli_num_rows($result) > 0) {$display_id = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td style="font-weight: bold;"><?php echo $display_id++; ?></td>
                <td>
                    <?php if(!empty($row['product_image'])) { ?>
                        <img src="<?php echo htmlspecialchars($row['product_image']); ?>" width="50" height="50" style="object-fit:cover; border-radius:5px;">
                    <?php } else { ?>
                        <span>Walang Larawan</span>
                    <?php } ?>
                </td>
                <td style="font-weight: bold; color: #1976d2;"><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['subcategory']); ?></td>
                <td style="color: #388e3c; font-weight: bold;">₱<?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['details']); ?></td>
                <td>
                    <a href="products.php?edit_id=<?php echo $row['id']; ?>" class="btn-action btn-edit">I-edit</a>
                    <a href="products.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Sigurado ka bang gusto mong burahin ang produktong ito?');" class="btn-action btn-delete">Burahin</a>
                </td>
            </tr>
            <?php 
                    }
                } else {
                    echo '<tr><td colspan="8" style="color: #d32f2f; font-weight: bold; padding: 25px;">Walang Listahan mula sa iyong produkto, magdagdag ka na lang para sa produkto</td></tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>