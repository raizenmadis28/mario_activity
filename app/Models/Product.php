<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected string $table = 'products';

    public function create(array $d): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO products (product_name, price, details, category, subcategory, date_added, product_image)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'sdsssss',
            $d['product_name'], $d['price'], $d['details'],
            $d['category'], $d['subcategory'], $d['date_added'], $d['product_image']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE products
             SET product_name=?, price=?, details=?, category=?, subcategory=?, product_image=?
             WHERE id=?"
        );
        $stmt->bind_param(
            'sdssssi',
            $d['product_name'], $d['price'], $d['details'],
            $d['category'], $d['subcategory'], $d['product_image'], $id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}