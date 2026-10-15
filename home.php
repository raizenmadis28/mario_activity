<?php
// home.php - Naglalaman lamang ng 3 button para sa Admin, Customer, at Inventory
?>
<div style="text-align: center; padding: 20px;">
    <h2 style="color: #d32f2f; text-shadow: 1px 1px #ffeb3b; margin-bottom: 30px;">Piliin ang Pamamahala ng Sistema</h2>

    <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
        <!-- Admin List Button -->
        <a href="admin_list.php" style="background: #ffebee; border: 3px solid #d32f2f; border-radius: 15px; padding: 25px 20px; width: 220px; text-decoration: none; color: #d32f2f; font-weight: bold; font-size: 18px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 35px; margin-bottom: 10px;">👑</div>
            Admin List
        </a>

        <!-- Customer List Button -->
        <a href="customer_list.php" style="background: #e3f2fd; border: 3px solid #1976d2; border-radius: 15px; padding: 25px 20px; width: 220px; text-decoration: none; color: #1976d2; font-weight: bold; font-size: 18px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 35px; margin-bottom: 10px;">👤</div>
            Customer List
        </a>

        <!-- Inventory List Button -->
        <a href="inventory_list.php" style="background: #e8f5e9; border: 3px solid #388e3c; border-radius: 15px; padding: 25px 20px; width: 220px; text-decoration: none; color: #388e3c; font-weight: bold; font-size: 18px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 35px; margin-bottom: 10px;">📦</div>
            Inventory List
        </a>
    </div>
</div>