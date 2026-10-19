<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin('/index.php');
        $model = new Customer();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $n  = trim($_POST['name'] ?? '');
            $em = trim($_POST['email'] ?? '');
            $ad = trim($_POST['address'] ?? '');
            $un = trim($_POST['username'] ?? '');
            $pw = $_POST['password'] ?? '';

            if ($n === '' || $un === '' || $pw === '') {
                toast('OHHH MAAAAMAAAMIAA!!! Punan ang name, username, password!', 'error');
            } else {
                $ok = $model->create([
                    'name' => $n, 'email' => $em, 'address' => $ad,
                    'username' => $un, 'password' => password_hash($pw, PASSWORD_DEFAULT),
                ]);
                toast($ok ? 'Customer added!' : 'Error: ' . $model->db()->error, $ok ? 'success' : 'error');
            }
            $this->redirect('/admin/customer_list.php');
        }

        if (isset($_GET['delete'])) {
            $ok = $model->delete((int)$_GET['delete']);
            toast($ok ? 'Customer deleted.' : 'Error sa delete.', $ok ? 'success' : 'error');
            $this->redirect('/admin/customer_list.php');
        }

        $this->view('admin/customer_list', [
            'title' => 'Customer List',
            'bg'    => '/Mario Wallpaper/NSMBWii2.png',
            'rows'  => $model->all('id DESC'),
        ]);
    }
}