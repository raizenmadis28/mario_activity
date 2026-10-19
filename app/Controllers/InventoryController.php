<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $model = new Inventory();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $n   = trim($_POST['product_name'] ?? '');
            $pr  = (float)($_POST['product_price'] ?? 0);
            $cat = trim($_POST['category'] ?? '');
            $sub = trim($_POST['subcategory'] ?? '');
            $det = trim($_POST['product_details'] ?? '');
            $img = '';

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $dir = __DIR__ . '/../../uploads';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $img = 'uploads/' . time() . '_' . basename($_FILES['photo']['name']);
                move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../../' . $img);
            }

            if ($n === '' || $pr <= 0) {
                toast('OHHH MAAAAMAAAMIAA!!! Punan ang name at price!', 'error');
            } else {
                $ok = $model->create([
                    'product_name' => $n, 'product_price' => $pr,
                    'category' => $cat, 'subcategory' => $sub,
                    'product_details' => $det, 'photo' => $img,
                ]);
                toast($ok ? 'Inventory item added!' : 'Error: ' . $model->db()->error, $ok ? 'success' : 'error');
            }
            $this->redirect('/admin/inventory_list.php');
        }

        if (isset($_GET['delete'])) {
            $ok = $model->delete((int)$_GET['delete']);
            toast($ok ? 'Item deleted.' : 'Error sa delete.', $ok ? 'success' : 'error');
            $this->redirect('/admin/inventory_list.php');
        }

        $this->view('admin/inventory_list', [
            'title' => 'Inventory',
            'bg'    => '/Mario Wallpaper/Power-up Panels.png',
            'rows'  => $model->all('id DESC'),
        ]);
    }
}