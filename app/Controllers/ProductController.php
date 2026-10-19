<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\ImageUpload;
use App\Models\Product;
use App\Models\Inventory;

class ProductController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $model   = new Product();
        $inv     = new Inventory();
        $isAdmin = Auth::isAdmin();
        $userId  = (int)($_SESSION['user_id'] ?? 0);

        // ===== POST (add/edit) — admin only =====
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isAdmin) {
                toast('Admin lang ang pwedeng mag-edit ng products.', 'error');
                $this->redirect('/products.php');
            }

            $action = $_POST['action'] ?? '';
            $name   = trim($_POST['product_name'] ?? '');
            $price  = (float)($_POST['price'] ?? 0);
            $stock  = (int)($_POST['stock'] ?? 0);
            $det    = trim($_POST['details'] ?? '');
            $cat    = trim($_POST['category'] ?? '');
            $sub    = trim($_POST['subcategory'] ?? '');
            $img    = $action === 'update' ? ($_POST['existing_image'] ?? '') : '';

            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $newImg = ImageUpload::handle($_FILES['product_image'], 'products');
                if ($newImg !== '') {
                    if ($action === 'update' && $img !== '') ImageUpload::delete($img);
                    $img = $newImg;
                }
            }

            if ($name === '' || $price <= 0 || $cat === '' || $sub === '') {
                toast('Punan lahat ng fields!', 'error');
            } elseif ($action === 'add') {
                $ok = $model->create([
                    'product_name'  => $name,
                    'price'         => $price,
                    'stock'         => $stock,
                    'details'       => $det,
                    'category'      => $cat,
                    'subcategory'   => $sub,
                    'date_added'    => date('Y-m-d H:i:s'),
                    'product_image' => $img,
                ]);
                if ($ok) {
                    $newId = (int)$model->db()->insert_id;
                    if ($stock > 0) {
                        $inv->log($newId, $name, 'INITIAL', $stock, 0, $stock, 'product_create', $newId, $userId, 'Bagong product na may initial stock');
                    }
                    toast('Produkto naidagdag!', 'success');
                } else {
                    toast('Error: ' . $model->db()->error, 'error');
                }
            } elseif ($action === 'update') {
                $id = (int)($_POST['id'] ?? 0);
                $oldProduct = $model->find($id);
                $oldStock = $oldProduct ? (int)$oldProduct['stock'] : 0;

                $ok = $model->update($id, [
                    'product_name'  => $name,
                    'price'         => $price,
                    'stock'         => $stock,
                    'details'       => $det,
                    'category'      => $cat,
                    'subcategory'   => $sub,
                    'product_image' => $img,
                ]);

                if ($ok) {
                    if ($oldStock !== $stock) {
                        $diff = $stock - $oldStock;
                        $act  = $diff > 0 ? 'IN' : 'ADJUSTMENT';
                        $note = $diff > 0
                            ? "Restock: +{$diff} units (from admin edit)"
                            : "Adjustment: {$diff} units (from admin edit)";
                        $inv->log($id, $name, $act, abs($diff), $oldStock, $stock, 'product_edit', $id, $userId, $note);
                    }
                    toast('Produkto na-update!', 'success');
                } else {
                    toast('Error: ' . $model->db()->error, 'error');
                }
            }
            $this->redirect('/products.php');
        }

        // ===== DELETE — admin only =====
        if (isset($_GET['delete'])) {
            if (!$isAdmin) {
                toast('Admin lang ang pwedeng mag-delete.', 'error');
                $this->redirect('/products.php');
            }
            $id = (int)$_GET['delete'];
            $p = $model->find($id);
            if ($p) {
                $oldStock = (int)$p['stock'];
                if ($oldStock > 0) {
                    $inv->log($id, $p['product_name'], 'ADJUSTMENT', $oldStock, $oldStock, 0, 'product_delete', $id, $userId, 'Product deleted — removed all stock');
                }
                if (!empty($p['product_image'])) ImageUpload::delete($p['product_image']);
            }
            $ok = $model->delete($id);
            toast($ok ? 'Produkto nabura.' : 'Error sa delete.', $ok ? 'success' : 'error');
            $this->redirect('/products.php');
        }

        $products = $model->all('id DESC');
        $edit = ($isAdmin && isset($_GET['edit'])) ? $model->find((int)$_GET['edit']) : null;

        $this->view('products', [
            'title'           => 'Products',
            'bg'              => '/Mario Wallpaper/Mario Galaxy Movie.jpg',
            'products'        => $products,
            'edit'            => $edit,
            'is_admin'        => $isAdmin,
            'cart_quantities' => $_SESSION['cart'] ?? [],
        ]);
    }
}