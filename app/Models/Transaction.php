<?php
namespace App\Models;

use App\Core\Model;

class Transaction extends Model
{
    protected string $table = 'transactions';

    public function create(array $d): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO transactions (customer_name, product_name, amount, payment_method, category, transaction_image)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('ssdsss',
            $d['customer_name'], $d['product_name'], $d['amount'],
            $d['payment_method'], $d['category'], $d['transaction_image']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}