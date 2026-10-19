<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\ImageUpload;
use App\Models\User;

class CustomerController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin('/index.php');
        $model = new User();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u  = trim($_POST['username'] ?? '');
            $em = trim($_POST['email'] ?? '');
            $n  = trim($_POST['full_name'] ?? '');
            $ad = trim($_POST['address'] ?? '');
            $p  = $_POST['password'] ?? '';
            $img = '';

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $img = ImageUpload::handle($_FILES['profile_image'], 'customers');
            }

            if ($u === '' || $em === '' || $p === '') {
                toast('Punan ang username, email, password!', 'error');
            } elseif ($model->exists($u, $em)) {
                toast('Ginamit na ang username/email.', 'error');
            } else {
                $id = $model->create([
                    'username' => $u, 'email' => $em,
                    'password' => password_hash($p, PASSWORD_DEFAULT),
                    'role'     => 'Customer',
                    'full_name' => $n, 'address' => $ad, 'profile_image' => $img,
                ]);
                toast($id ? 'Customer added!' : 'Error: ' . $model->db()->error, $id ? 'success' : 'error');
            }
            $this->redirect('/admin/customer_list.php');
        }

        if (isset($_GET['delete'])) {
            $ok = $model->deleteByRole((int)$_GET['delete'], 'Customer');
            toast($ok ? 'Customer deleted.' : 'Error sa delete.', $ok ? 'success' : 'error');
            $this->redirect('/admin/customer_list.php');
        }

        $this->view('admin/customer_list', [
            'title' => 'Customer List',
            'bg'    => '/Mario Wallpaper/NSMBWii2.png',
            'rows'  => $model->allByRole('Customer'),
        ]);
    }
}