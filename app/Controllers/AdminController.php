<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin('/index.php');
        $model = new Admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = trim($_POST['username'] ?? '');
            $p = $_POST['password'] ?? '';

            if ($u === '' || $p === '') {
                toast('OHHH!!! MAAAMMMAAAMIAAA!!! Punan lahat ng fields!', 'error');
            } else {
                $ok = $model->create($u, password_hash($p, PASSWORD_DEFAULT));
                toast($ok ? 'Admin added!' : 'Error: ' . $model->db()->error, $ok ? 'success' : 'error');
            }
            $this->redirect('/admin/admin_list.php');
        }

        if (isset($_GET['delete'])) {
            $ok = $model->delete((int)$_GET['delete']);
            toast($ok ? 'Admin deleted.' : 'Error sa delete.', $ok ? 'success' : 'error');
            $this->redirect('/admin/admin_list.php');
        }

        $this->view('admin/admin_list', [
            'title' => 'Admin List',
            'bg'    => '/Mario Wallpaper/NSMBWii.png',
            'rows'  => $model->all('id DESC'),
        ]);
    }
}