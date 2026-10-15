<?php
// index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'Model/DB_Model.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Kunin ang username at role ng naka-login (Ginawang case-insensitive para masigurong sasakto sa Admin)
$current_user = isset($_SESSION['username']) ? $_SESSION['username'] : 'ChocoZero28';
$current_role = isset($_SESSION['role']) ? trim($_SESSION['role']) : 'Admin';
?>
<html>
<head>
<title>RaizenMadis28 Inventory Anime System</title>
<style>
    body {
        background-image: url('Mario Wallpaper/NSMBUMushroomKingdom.png');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
        font-family: "Comic Sans MS", "Chalkboard SE", sans-serif;
        color: #000;
        text-align: center;
        margin: 0;
        padding: 0;
    }
    #mainWrapper {
        background-color: rgba(255, 255, 255, 0.93);
        border-radius: 20px;
        padding: 30px;
        margin: 5vh auto;
        width: 85%;
        max-width: 1000px;
        border: 4px solid #000;
        box-shadow: 0px 8px 16px rgba(0,0,0,0.3);
    }
    h1 {
        color: #d32f2f;
        text-shadow: 2px 2px #ffeb3b;
        margin-bottom: 5px;
    }
    .nav-links {
        margin: 20px 0 20px 0;
        font-size: 16px;
        font-weight: bold;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .nav-links a {
        color: #1976d2;
        text-decoration: none;
        padding: 8px 18px;
        border: 2px solid #1976d2;
        border-radius: 10px;
        background: #e3f2fd;
        transition: 0.3s;
    }
    .nav-links a:hover, .nav-links a.active {
        background: #1976d2;
        color: #fff;
    }
    .logout-btn {
        color: #d32f2f !important;
        border: 2px solid #d32f2f !important;
        background: #ffebee !important;
    }
    .logout-btn:hover {
        background: #d32f2f !important;
        color: #fff !important;
    }

    .dashboard-title {
        font-size: 24px;
        font-weight: 900;
        color: #d32f2f;
        margin: 20px 0;
    }

    .menu-grid {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .menu-card {
        background-color: #fff;
        border: 3px solid #ccc;
        border-radius: 15px;
        padding: 25px 15px;
        width: 250px;
        text-decoration: none;
        color: #000;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
    }

    .menu-card:hover {
        transform: translateY(-5px);
    }

    .card-admin {
        border-color: #e57373;
        background-color: #fff5f5;
    }
    .card-admin:hover {
        background-color: #ffebee;
        border-color: #d32f2f;
    }

    .card-customer {
        border-color: #64b5f6;
        background-color: #f0f7ff;
    }
    .card-customer:hover {
        background-color: #e3f2fd;
        border-color: #1976d2;
    }

    .card-inventory {
        border-color: #81c784;
        background-color: #f1f8e9;
    }
    .card-inventory:hover {
        background-color: #e8f5e9;
        border-color: #388e3c;
    }

    .card-products {
        border-color: #f06292;
        background-color: #fce4ec;
    }
    .card-products:hover {
        background-color: #f8bbd0;
        border-color: #e91e63;
    }

    .card-transactions {
        border-color: #ffd54f;
        background-color: #fffde7;
    }
    .card-transactions:hover {
        background-color: #fff9c4;
        border-color: #ffb300;
    }

    .menu-card h3 {
        margin: 15px 0 5px 0;
        font-size: 20px;
        font-weight: bold;
    }
    
    .card-admin h3 { color: #d32f2f; }
    .card-customer h3 { color: #1976d2; }
    .card-inventory h3 { color: #388e3c; }
    .card-products h3 { color: #c2185b; }
    .card-transactions h3 { color: #f57f17; }
</style>
</head>
<body>

<div id="mainWrapper">
    <h1>🍄 RaizenMadis28 Inventory Anime System 🍄</h1>
    <p>Maligayang pagdating sa iyong sistema, <?php echo htmlspecialchars($current_user); ?> (Role: <?php echo htmlspecialchars($current_role); ?>)!</p>

    <!-- Top Navigation Menu -->
    <div class="nav-links">
        <a href="index.php?page=home" class="<?php echo ($page == 'home') ? 'active' : ''; ?>">🏠 Home</a>
        <a href="index.php?page=about" class="<?php echo ($page == 'about') ? 'active' : ''; ?>">ℹ️ About</a>
        <a href="index.php?page=contact" class="<?php echo ($page == 'contact') ? 'active' : ''; ?>">📞 Contact</a>
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </div>

    <hr style="border: 1px solid #ccc; margin: 20px 0;" />

    <?php
    if ($page == 'about') {
        if (file_exists('about.php')) { include('about.php'); } else { echo "<p>Wala pang about.php file.</p>"; }
    } elseif ($page == 'contact') {
        if (file_exists('contact.php')) { include('contact.php'); } else { echo "<p>Wala pang contact.php file.</p>"; }
    } else {
        ?>
        <div class="dashboard-title">Piliin ang Pamamahala ng Sistema</div>
        
        <div class="menu-grid">
            <?php if (strcasecmp($current_role, 'Admin') == 0): ?>
                <!-- ADMIN: Kumpletong 5 Modules -->
                <a href="admin/admin_list.php" class="menu-card card-admin">
                    <div style="font-size: 40px;">👑</div>
                    <h3>Admin List</h3>
                </a>

                <a href="admin/customer_list.php" class="menu-card card-customer">
                    <div style="font-size: 40px;">👤</div>
                    <h3>Customer List</h3>
                </a>

                <a href="admin/inventory_list.php" class="menu-card card-inventory">
                    <div style="font-size: 40px;">📦</div>
                    <h3>Inventory List</h3>
                </a>

                <a href="products.php" class="menu-card card-products">
                    <div style="font-size: 40px;">🛍️</div>
                    <h3>Products</h3>
                </a>

                <a href="transactions.php" class="menu-card card-transactions">
                    <div style="font-size: 40px;">💳</div>
                    <h3>Transactions</h3>
                </a>

            <?php else: ?>
                <!-- CUSTOMER: 3 Restricted Modules (Products, Inventory List, Transactions) -->
                <a href="products.php" class="menu-card card-products">
                    <div style="font-size: 40px;">🛍️</div>
                    <h3>Products</h3>
                </a>

                <a href="admin/inventory_list.php" class="menu-card card-inventory">
                    <div style="font-size: 40px;">📦</div>
                    <h3>Inventory List</h3>
                </a>

                <a href="transactions.php" class="menu-card card-transactions">
                    <div style="font-size: 40px;">💳</div>
                    <h3>Transactions</h3>
                </a>
            <?php endif; ?>
        </div>
        <?php
    }
    ?>

</div>

</body>
</html>