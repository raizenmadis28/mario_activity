<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Inventory;
use App\Models\User;

class CartController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $productModel = new Product();

        // ===== POST ACTIONS =====
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add') {
                $pid = (int)($_POST['product_id'] ?? 0);
                $qty = max(1, (int)($_POST['quantity'] ?? 1));
                $product = $productModel->find($pid);

                if (!$product) {
                    toast('Product not found.', 'error');
                } elseif ((int)$product['stock'] < 1) {
                    toast('Out of stock.', 'error');
                } else {
                    $current   = $_SESSION['cart'][$pid] ?? 0;
                    $available = (int)$product['stock'] - $current;

                    if ($available <= 0) {
                        toast("⚠️ Nasa cart mo na lahat ng stock ng '{$product['product_name']}'.", 'warning');
                    } else {
                        $addQty = min($qty, $available);
                        $_SESSION['cart'][$pid] = $current + $addQty;

                        if ($addQty < $qty) {
                            toast("⚠️ Na-add lang {$addQty} pcs ng '{$product['product_name']}' (naubos na ang stock).", 'warning');
                        } else {
                            toast("✅ Na-add sa cart: {$product['product_name']} x{$addQty}", 'success');
                        }
                    }
                }
                $this->redirect('/products.php');
            }

            if ($action === 'update') {
                $pid = (int)($_POST['product_id'] ?? 0);
                $qty = (int)($_POST['quantity'] ?? 0);
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$pid]);
                } else {
                    $product = $productModel->find($pid);
                    if ($product && $qty <= (int)$product['stock']) {
                        $_SESSION['cart'][$pid] = $qty;
                        toast('Cart updated.', 'success');
                    } else {
                        toast('Quantity exceeds stock.', 'error');
                    }
                }
                $this->redirect('/cart.php');
            }

            if ($action === 'remove') {
                $pid = (int)($_POST['product_id'] ?? 0);
                unset($_SESSION['cart'][$pid]);
                toast('Item removed.', 'info');
                $this->redirect('/cart.php');
            }

            if ($action === 'clear') {
                $_SESSION['cart'] = [];
                toast('Cart cleared.', 'info');
                $this->redirect('/cart.php');
            }

            if ($action === 'checkout') {
                $this->placeOrder();
                return;
            }

            $this->redirect('/cart.php');
        }

        // ===== GET: load cart =====
        $cart = $_SESSION['cart'] ?? [];
        $items = [];
        $total = 0;

        foreach ($cart as $pid => $qty) {
            $p = $productModel->find((int)$pid);
            if (!$p) continue;
            $subtotal = (float)$p['price'] * (int)$qty;
            $items[] = [
                'id' => $pid,
                'name' => $p['product_name'],
                'image' => $p['product_image'],
                'price' => (float)$p['price'],
                'qty' => (int)$qty,
                'subtotal' => $subtotal,
                'stock' => (int)$p['stock'],
                'category' => $p['category'],
            ];
            $total += $subtotal;
        }

        // Auto-fill fallback: full_name → username → ''
        $u = Auth::user();
        $dbUser = (new User())->find((int)$u['id']);

        $defaultName = trim($dbUser['full_name'] ?? '') !== ''
            ? $dbUser['full_name']
            : ($dbUser['username'] ?? '');

        $defaultAddr = trim($dbUser['address'] ?? '');

        $this->view('cart', [
            'title'        => 'My Cart',
            'bg'           => '/Mario Wallpaper/Mario 3D World.jpeg',
            'items'        => $items,
            'total'        => $total,
            'default_name' => $defaultName,
            'default_addr' => $defaultAddr,
        ]);
    }

    private function placeOrder(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            toast('Walang laman ang cart mo.', 'error');
            $this->redirect('/products.php');
            return;
        }

        $name = trim($_POST['customer_name'] ?? '');
        $addr = trim($_POST['delivery_address'] ?? '');
        $pay  = trim($_POST['payment_method'] ?? '');

        if ($name === '' || $addr === '' || $pay === '') {
            toast('Punan lahat ng checkout fields.', 'error');
            $this->redirect('/cart.php');
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $productModel = new Product();
        $txnModel = new Transaction();
        $invModel = new Inventory();
        $db = Database::conn();

        $db->begin_transaction();
        try {
            foreach ($cart as $pid => $qty) {
                $qty = (int)$qty;
                if ($qty < 1) continue;

                $p = $productModel->find((int)$pid);
                if (!$p) throw new \Exception("Product #{$pid} not found");

                $oldStock = (int)$p['stock'];
                if ($oldStock < $qty) {
                    throw new \Exception("Kulang ang stock ng '{$p['product_name']}' (may {$oldStock} lang)");
                }

                if (!$productModel->decrementStock((int)$pid, $qty)) {
                    throw new \Exception("Stock changed for '{$p['product_name']}'");
                }

                $ok = $txnModel->create([
                    'user_id'          => $userId,
                    'product_id'       => (int)$pid,
                    'customer_name'    => $name,
                    'product_name'     => $p['product_name'],
                    'amount'           => (float)$p['price'],
                    'quantity'         => $qty,
                    'payment_method'   => $pay,
                    'delivery_address' => $addr,
                    'category'         => $p['category'],
                    'transaction_image'=> '',
                ]);
                if (!$ok) throw new \Exception("DB insert failed: " . $db->error);

                $txnId = (int)$db->insert_id;
                $newStock = $oldStock - $qty;

                $invModel->log(
                    (int)$pid,
                    $p['product_name'],
                    'OUT',
                    $qty,
                    $oldStock,
                    $newStock,
                    'order',
                    $txnId,
                    $userId,
                    "Binenta kay {$name} — order #{$txnId}"
                );
            }
            $db->commit();
            $_SESSION['cart'] = [];
            toast('🎉 Order placed successfully! Naka-record na sa transactions.', 'success');
            $this->redirect('/transactions.php');
        } catch (\Exception $e) {
            $db->rollback();
            toast('❌ Checkout failed: ' . $e->getMessage(), 'error');
            $this->redirect('/cart.php');
        }
    }
}