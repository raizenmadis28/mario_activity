<?php
namespace App\Models;
use App\Core\Model;

class Inventory extends Model
{
    protected string $table = 'inventory';

    public function create(array $d): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO inventory (product_name, product_price, category, subcategory, product_details, photo)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->bind_param('sdssss',
            $d['product_name'], $d['product_price'], $d['category'],
            $d['subcategory'], $d['product_details'], $d['photo']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}