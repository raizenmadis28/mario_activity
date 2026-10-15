<?php
// Database Connection Settings
$host = "localhost";
$user = "root";
$pass = "@RBmadis28";
$db   = "Mario_Activity"; // Palitan kung iba ang pangalan ng database mo

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function para sa pag-redirect ng pahina
function redirect_to($new_location) {
    header("Location: " . $new_location);
    exit;
}

// Function para mag-save o mag-insert sa database
function save($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

// Function para mag-display ng mga listahan at maglagay ng action buttons at images
function display_all($sql, $column_mappings, $page_name) {
    global $conn;
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse:collapse; background:#fff; color:#000;'>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($column_mappings as $col_key => $col_label) {
                echo "<td>";
                echo $col_label;
                if (isset($row[$col_key])) {
                    // Kung may image column at may laman ang file name, ipalabas ang <img> tag
                    if ($col_key == 'image' && !empty($row[$col_key])) {
                        echo "<br><img src='uploads/" . htmlspecialchars($row[$col_key]) . "' width='60' height='60' style='object-fit:cover; border-radius:5px; border:1px solid #000;' />";
                    } else {
                        echo htmlspecialchars($row[$col_key]);
                    }
                }
                echo "</td>";
            }
            // Delete Button kasama ang ID
            $id = $row['id'] ?? 0;
            echo "<td align='center'><a href='?delete_id=$id' onclick=\"return confirm('Sigurado ka bang gusto mong burahin ito pati ang kaugnay na file?');\" style='color:red; text-decoration:none; font-weight:bold;'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Walang nakitang rekord.</p>";
    }
}

// Auto-handle ng pag-delete ng record at pagbura ng file sa folder kapag pinindot ang Delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $current_script = basename($_SERVER['PHP_SELF']);
    
    // Alamin kung saang table nabibilang ang kasalukuyang pahina
    $table = "";
    if ($current_script == "customer_list.php") {
        $table = "customer";
    } elseif ($current_script == "admin_list.php") {
        $table = "admin";
    } elseif ($current_script == "inventory_list.php") {
        $table = "products";
    } elseif ($current_script == "index.php" || $current_script == "home.php") {
        $table = "customer"; // O depende sa kung aling table ang ide-delete mula sa home
    }

    if (!empty($table)) {
        // 1. Kunin muna ang impormasyon sa database para mahanap ang image filename bago i-delete
        $check_query = mysqli_query($conn, "SELECT * FROM $table WHERE id = $id");
        if ($check_query && mysqli_num_rows($check_query) > 0) {
            $data = mysqli_fetch_assoc($check_query);
            
            // Kung may 'image' column at may laman ito, burahin ang file mula sa uploads folder
            if (isset($data['image']) && !empty($data['image'])) {
                $file_path = "uploads/" . $data['image'];
                if (file_exists($file_path)) {
                    unlink($file_path); // Dito tuluyang mawawala ang file sa folder
                }
            }
        }

        // 2. I-delete ang buong record mula sa database table
        mysqli_query($conn, "DELETE FROM $table WHERE id = $id");
    }

    redirect_to($current_script);
}
?>