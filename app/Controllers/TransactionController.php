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
        $isAdmin = Auth::isAdmin();

        // DELETE — admin only
        if (isset($_GET['delete'])) {
            if (!$isAdmin) {
                toast('Admin lang ang pwedeng mag-delete ng transaction.', 'error');
                $this->redirect('/transactions.php');
            }
            $ok = $model->delete((int)$_GET['delete']);
            toast($ok ? 'Transaction deleted.' : 'Error sa delete.', $ok ? 'success' : 'error');
            $this->redirect('/transactions.php');
        }

        $rows = $isAdmin
            ? $model->allWithCustomer()
            : $model->allByUser((int)($_SESSION['user_id'] ?? 0));

        $this->view('transactions', [
            'title'    => 'Transactions',
            'bg'       => '/Mario Wallpaper/Mario 3D World.jpeg',
            'rows'     => $rows,
            'is_admin' => $isAdmin,
        ]);
    }
}