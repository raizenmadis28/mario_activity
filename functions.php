<?php
// functions.php - Raizeness Inventory Anime System

/**
 * Nagbabalik ng tamang image folder path batay sa kategorya ng produkto.
 */
function getProductFolder($category) {
    $folder = "Gundam Images/";
    if ($category == 'Code Geass') {
        $folder = "Code Geass/";
    } elseif ($category == 'Transformers') {
        $folder = "Transformers Images/";
    } elseif ($category == 'Valorants') {
        $folder = "Valorants/";
    }
    return $folder;
}

/**
 * Kinakalkula ang kabuuang halaga ng imbentaryo (Price x Stock o katulad na computation kung kinakailangan).
 */
function calculateInventoryTotal($price, $quantity) {
    return $price * $quantity;
}

/**
 * Pangkalahatang greeting para sa Raizeness System
 */
function systemGreeting() {
    echo "Welcome sa Raizeness Inventory Anime System! 🎮🛍️";
}
?>