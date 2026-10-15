<?php
// transactions.php - Raizeness Inventory Anime System
require_once 'Model/DB_Model.php';

if (!isset($connection) && isset($conn)) {
    $connection = $conn;
}

// Awtomatikong gawan ng table kung hindi pa umiiral
if (isset($connection)) {
    $connection->query("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(100) NOT NULL,
        category VARCHAR(100) NOT NULL,
        transaction_image VARCHAR(255),
        transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

$msg = "";

// PROSESO NG PAGDARAGDAG NG TRANSAKSYON
if (isset($_POST['add_transaction']) && isset($connection)) {
    $customer_name = $_POST['customer_name'];
    $product_name = $_POST['product_name'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $category = $_POST['category'];

    $img_name = $_FILES['transaction_image']['name'];
    $img_tmp = $_FILES['transaction_image']['tmp_name'];
    
    // Pagpili ng tamang folder batay sa kategorya
    $folder = "Gundam Images/";
    if ($category == 'Code Geass') {
        $folder = "Code Geass/";
    } elseif ($category == 'Transformers') {
        $folder = "Transformers Images/";
    } elseif ($category == 'Valorants') {
        $folder = "Valorants/";
    } elseif ($category == 'Mario Meme') {
        $folder = "Mario Meme/";
    }
    
    $target_file = "";
    if (!empty($img_name)) {
        $target_file = $folder . basename($img_name);
        move_uploaded_file($img_tmp, $target_file);
    }

    $stmt = $connection->prepare("INSERT INTO transactions (customer_name, product_name, amount, payment_method, category, transaction_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $customer_name, $product_name, $amount, $payment_method, $category, $target_file);
    
    if ($stmt->execute()) {
        $msg = "Tagumpay na naidagdag ang transaksyon!";
    } else {
        $msg = "May error sa pagdagdag ng transaksyon.";
    }
    $stmt->close();
}

// PROSESO NG DELETE NG TRANSAKSYON
if (isset($_GET['delete_id']) && isset($connection)) {
    $del_id = intval($_GET['delete_id']);
    $stmt = $connection->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>window.location.href='transactions.php';</script>";
    exit();
}
?>
<html>
<head>
<title>Transactions - Raizeness Inventory Anime System</title>
<style>
    body {
        background-image: url('Mario Wallpaper/Mario 3D World.jpeg');
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
    .form-container input, .form-container select {
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
        background-color: #4caf50;
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
    }
    .btn-delete { background: #f44336; }
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
    <h2>💰 Raizeness Inventory - Customer Transactions 💰</h2>
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="index.php?page=home" class="nav-back">🏠 Bumalik sa Home</a>
        <button onclick="toggleAddForm()" class="btn-add">➕ Magdagdag ng Transaksyon</button>
    </div>

    <?php if (!empty($msg)) echo "<p style='color: green; font-weight: bold; text-align: center;'>$msg</p>"; ?>

    <!-- ADD TRANSACTION FORM -->
    <div id="addFormContainer" class="form-container">
        <h3>Magdagdag ng Bagong Transaksyon (Customer)</h3>
        <form action="transactions.php" method="POST" enctype="multipart/form-data">
            <label>Pangalan ng Customer:</label>
            <input type="text" name="customer_name" required>
            
            <label>Piliin ang Kategorya ng Produkto:</label>
            <select name="category" required>
                <option value="">-- Piliin ang Kategorya --</option>
                <option value="Gundam">Gundam</option>
                <option value="Code Geass">Code Geass</option>
                <option value="Transformers">Transformers</option>
                <option value="Valorants">Valorants</option>
                <option value="Manga">Manga</option>
            </select>

            <label>Pangalan ng Produktong Bibilhin:</label>
            <input type="text" name="product_name" required>
            
            <label>Halaga / Bayad (₱):</label>
            <input type="number" step="0.01" name="amount" required>
            
            <label>Saan Babayad (Payment Method):</label>
            <select name="payment_method" required>
                <option value="Cash on Delivery (COD)">Cash on Delivery (COD)</option>
                <option value="GCash">GCash</option>
                <option value="Maya">Maya</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>

            <label>Pumili ng Larawan (Choose Image):</label>
            <input type="file" name="transaction_image" accept="image/*" required>
            
            <button type="submit" name="add_transaction" style="background: #388e3c; color: white; padding: 8px 15px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; width: 100%;">I-save ang Transaksyon</button>
        </form>
    </div>

    <!-- TRANSACTIONS TABLE -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Larawan</th>
                <th>Pangalan ng Customer</th>
                <th>Kategorya</th>
                <th>Produktong Binili</th>
                <th>Bayad (₱)</th>
                <th>Payment Method</th>
                <th>Aksyon</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($connection)) {
                $result = mysqli_query($connection, "SELECT * FROM transactions");
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td style="font-weight: bold;"><?php echo $row['id']; ?></td>
                <td>
                    <?php if(!empty($row['transaction_image'])) { ?>
                        <img src="<?php echo htmlspecialchars($row['transaction_image']); ?>" width="50" height="50" style="object-fit:cover; border-radius:5px;">
                    <?php } else { ?>
                        <span>Walang Larawan</span>
                    <?php } ?>
                </td>
                <td style="font-weight: bold; color: #1976d2;"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td style="color: #388e3c; font-weight: bold;">₱<?php echo number_format($row['amount'], 2); ?></td>
                <td><span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 5px; font-weight: bold;"><?php echo htmlspecialchars($row['payment_method']); ?></span></td>
                <td>
                    <a href="transactions.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Sigurado ka bang gusto mong burahin ang transaksyong ito?');" class="btn-action btn-delete">Burahin</a>
                </td>
            </tr>
            <?php 
                    }
                } else {
                    echo '<tr><td colspan="8" style="color: #d32f2f; font-weight: bold; padding: 25px;">Walang nakitang mga transaksyon sa database. Pindutin ang "Magdagdag ng Transaksyon" sa itaas para magsimula.</td></tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>