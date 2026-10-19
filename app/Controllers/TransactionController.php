<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $model = new Transaction();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $c   = trim($_POST['customer_name'] ?? '');
            $p   = trim($_POST['product_name'] ?? '');
            $a   = (float)($_POST['amount'] ?? 0);
            $pm  = trim($_POST['payment_method'] ?? '');
            $cat = trim($_POST['category'] ?? '');
            $img = '';

            if (isset($_FILES['transaction_image']) && $_FILES['transaction_image']['error'] === UPLOAD_ERR_OK) {
                $dir = __DIR__ . '/../../uploads';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $img = 'uploads/' . time() . '_' . basename($_FILES['transaction_image']['name']);
                move_uploaded_file($_FILES['transaction_image']['tmp_name'], __DIR__ . '/../../' . $img);
            }

            if ($c === '' || $p === '' || $a <= 0 || $pm === '' || $cat === '') {
                toast('Punan lahat ng fields!', 'error');
            } else {
                $ok = $model->create([
                    'customer_name'     => $c,
                    'product_name'      => $p,
                    'amount'            => $a,
                    'payment_method'    => $pm,
                    'category'          => $cat,
                    'transaction_image' => $img,
                ]);
                toast($ok ? 'Transaction added!' : 'Error: ' . $model->db()->error, $ok ? 'success' : 'error');
            }
            $this->redirect('/transactions.php');
        }

        if (isset($_GET['delete'])) {
            $ok = $model->delete((int)$_GET['delete']);
            toast($ok ? 'Transaction deleted.' : 'Error sa delete.', $ok ? 'success' : 'error');
            $this->redirect('/transactions.php');
        }

        $this->view('transactions', [
            'title' => 'Transactions',
            'bg'    => '/Mario Wallpaper/Mario 3D World.jpeg',
            'rows'  => $model->all('id DESC'),
        ]);
    }
}