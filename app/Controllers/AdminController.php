<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin('/index.php');
        $model = new User();
        $currentId = (int)($_SESSION['user_id'] ?? 0);

        // ===== POST: Add admin =====
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u  = trim($_POST['username'] ?? '');
            $em = trim($_POST['email'] ?? '');
            $p  = $_POST['password'] ?? '';

            if ($u === '' || $em === '' || $p === '') {
                toast('Punan lahat ng fields!', 'error');
            } elseif (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
                toast('Hindi valid ang email.', 'error');
            } elseif (strlen($p) < 6) {
                toast('Dapat 6+ characters ang password.', 'error');
            } elseif ($model->exists($u, $em)) {
                toast('Ginamit na ang username/email.', 'error');
            } else {
                $id = $model->create([
                    'username' => $u,
                    'email'    => $em,
                    'password' => password_hash($p, PASSWORD_DEFAULT),
                    'role'     => 'Admin',
                ]);
                toast($id ? 'Admin added! Pwede nang mag-login.' : 'Error: ' . $model->db()->error, $id ? 'success' : 'error');
            }
            $this->redirect('/admin/admin_list.php');
        }

        // ===== DELETE: Admin only, hindi pwedeng sarili =====
        if (isset($_GET['delete'])) {
            $delId = (int)$_GET['delete'];

            if ($delId === $currentId) {
                toast('⚠️ Hindi mo pwedeng i-delete ang sarili mong account.', 'warning');
            } else {
                $ok = $model->deleteByRole($delId, 'Admin');
                toast($ok ? 'Admin deleted.' : 'Error sa delete.', $ok ? 'success' : 'error');
            }
            $this->redirect('/admin/admin_list.php');
        }

        // ===== LIST: Exclude current admin =====
        $allAdmins = $model->allByRole('Admin');
        $rows = array_values(array_filter($allAdmins, function ($r) use ($currentId) {
            return (int)$r['id'] !== $currentId;
        }));

        // Get current admin info para ma-display sa "viewing as" banner
        $currentAdmin = $model->find($currentId);

        $this->view('admin/admin_list', [
            'title'         => 'Admin List',
            'bg'            => '/Mario Wallpaper/NSMBWii.png',
            'rows'          => $rows,
            'current_admin' => $currentAdmin,
        ]);
    }
}