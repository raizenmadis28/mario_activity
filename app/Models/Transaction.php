<?php
namespace App\Models;

use App\Core\Model;

class Transaction extends Model
{
    protected string $table = 'transactions';

    public function create(array $d): bool
    {
        $userId   = $d['user_id'] ?? null;
        $prodId   = $d['product_id'] ?? null;
        $custName = $d['customer_name'];
        $prodName = $d['product_name'];
        $amount   = (float)$d['amount'];
        $qty      = (int)($d['quantity'] ?? 1);
        $pay      = $d['payment_method'];
        $addr     = $d['delivery_address'] ?? '';
        $cat      = $d['category'];
        $img      = $d['transaction_image'] ?? '';

        $stmt = $this->db->prepare(
            "INSERT INTO transactions (user_id, product_id, customer_name, product_name, amount, quantity, payment_method, delivery_address, category, transaction_image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('iissdissss',
            $userId, $prodId, $custName, $prodName, $amount, $qty, $pay, $addr, $cat, $img
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function allByUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE user_id=? ORDER BY id DESC");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public function allWithCustomer(): array
    {
        return $this->db->query(
            "SELECT t.*, u.username AS customer_username, u.email AS customer_email
             FROM transactions t
             LEFT JOIN users u ON u.id = t.user_id
             ORDER BY t.id DESC"
        )->fetch_all(MYSQLI_ASSOC);
    }
}