<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin('/index.php');

        $productModel = new Product();
        $invModel = new Inventory();

        $products = $productModel->all('id DESC');

        // Stats
        $totalProducts = count($products);
        $totalStock    = 0;
        $lowStock      = [];
        $outOfStock    = [];
        $totalValue    = 0.0;

        foreach ($products as $p) {
            $stock = (int)$p['stock'];
            $totalStock += $stock;
            $totalValue += (float)$p['price'] * $stock;
            if ($stock === 0) $outOfStock[] = $p;
            elseif ($stock < 5) $lowStock[] = $p;
        }

        $filter = $_GET['filter'] ?? 'all';
        if ($filter === 'low')      $display = $lowStock;
        elseif ($filter === 'out')  $display = $outOfStock;
        else                        $display = $products;

        $this->view('admin/inventory_list', [
            'title'          => 'Inventory Monitoring',
            'bg'             => '/Mario Wallpaper/Power-up Panels.png',
            'rows'           => $display,
            'filter'         => $filter,
            'total_products' => $totalProducts,
            'total_stock'    => $totalStock,
            'total_value'    => $totalValue,
            'low_count'      => count($lowStock),
            'out_count'      => count($outOfStock),
            'total_in'       => $invModel->totalIn(),
            'total_out'      => $invModel->totalOut(),
            'history'        => $invModel->allWithUser(),
        ]);
    }
}