<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $model = new Product();

        // ===== POST (Add / Update) =====
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $name   = trim($_POST['product_name'] ?? '');
            $price  = (float)($_POST['price'] ?? 0);
            $det    = trim($_POST['details'] ?? '');
            $cat    = trim($_POST['category'] ?? '');
            $sub    = trim($_POST['subcategory'] ?? '');
            $img    = $action === 'update' ? ($_POST['existing_image'] ?? '') : '';

            // File upload
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $dir = __DIR__ . '/../../uploads';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $img = 'uploads/' . time() . '_' . basename($_FILES['product_image']['name']);
                move_uploaded_file($_FILES['product_image']['tmp_name'], __DIR__ . '/../../' . $img);
            }

            if ($name === '' || $price <= 0 || $cat === '' || $sub === '') {
                toast('OHHH!! Punan lahat ng fields at tamang presyo!', 'error');
            } elseif ($action === 'add') {
                $ok = $model->create([
                    'product_name' => $name,
                    'price'        => $price,
                    'details'      => $det,
                    'category'     => $cat,
                    'subcategory'  => $sub,
                    'date_added'   => date('Y-m-d H:i:s'),
                    'product_image'=> $img,
                ]);
                toast($ok ? 'Produkto naidagdag!' : 'Error: ' . $model->db()->error, $ok ? 'success' : 'error');
            } elseif ($action === 'update') {
                $ok = $model->update((int)($_POST['id'] ?? 0), [
                    'product_name' => $name,
                    'price'        => $price,
                    'details'      => $det,
                    'category'     => $cat,
                    'subcategory'  => $sub,
                    'product_image'=> $img,
                ]);
                toast($ok ? 'Produkto na-update!' : 'Error: ' . $model->db()->error, $ok ? 'success' : 'error');
            }
            $this->redirect('/products.php');
        }

        // ===== DELETE =====
        if (isset($_GET['delete'])) {
            $ok = $model->delete((int)$_GET['delete']);
            toast($ok ? 'Produkto nabura.' : 'Error sa delete.', $ok ? 'success' : 'error');
            $this->redirect('/products.php');
        }

        // ===== LOAD DATA =====
        $products = $model->all('id DESC');
        $edit     = isset($_GET['edit']) ? $model->find((int)$_GET['edit']) : null;

        // ===== RENDER VIEW =====
        $this->view('products', [
            'title'    => 'Products',
            'bg'       => '/Mario Wallpaper/Mario Galaxy Movie.jpg',
            'products' => $products,
            'edit'     => $edit,
        ]);
    }
}