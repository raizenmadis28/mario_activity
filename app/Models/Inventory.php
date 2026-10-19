<?php
namespace App\Models;

use App\Core\Model;

class Inventory extends Model
{
    protected string $table = 'inventory';

    /**
     * Log a stock movement.
     * @param string $action 'IN' | 'OUT' | 'ADJUSTMENT' | 'INITIAL'
     */
    public function log(
        int $productId,
        string $productName,
        string $action,
        int $quantity,
        int $oldStock,
        int $newStock,
        string $refType = 'manual',
        int $refId = 0,
        int $userId = 0,
        string $notes = ''
    ): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO inventory
              (product_id, product_name, action, quantity, old_stock, new_stock, reference_type, reference_id, user_id, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return false;

        $stmt->bind_param(
            'issiiisiss',
            $productId, $productName, $action, $quantity,
            $oldStock, $newStock, $refType, $refId, $userId, $notes
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function allWithUser(): array
    {
        return $this->db->query(
            "SELECT i.*, u.username AS user_name
             FROM inventory i
             LEFT JOIN users u ON u.id = i.user_id
             ORDER BY i.id DESC
             LIMIT 500"
        )->fetch_all(MYSQLI_ASSOC);
    }

    public function byProduct(int $productId): array
    {
        $stmt = $this->db->prepare(
            "SELECT i.*, u.username AS user_name
             FROM inventory i
             LEFT JOIN users u ON u.id = i.user_id
             WHERE i.product_id = ?
             ORDER BY i.id DESC"
        );
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public function totalIn(): int
    {
        $r = $this->db->query(
            "SELECT COALESCE(SUM(quantity),0) AS t FROM inventory WHERE action IN ('IN','INITIAL')"
        )->fetch_assoc();
        return (int)($r['t'] ?? 0);
    }

    public function totalOut(): int
    {
        $r = $this->db->query(
            "SELECT COALESCE(SUM(quantity),0) AS t FROM inventory WHERE action='OUT'"
        )->fetch_assoc();
        return (int)($r['t'] ?? 0);
    }
}