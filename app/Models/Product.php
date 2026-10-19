<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected string $table = 'products';

    public function create(array $d): bool
    {
        $name    = $d['product_name'];
        $price   = (float)$d['price'];
        $stock   = (int)($d['stock'] ?? 0);
        $details = $d['details'] ?? '';
        $cat     = $d['category'];
        $sub     = $d['subcategory'];
        $date    = $d['date_added'];
        $img     = $d['product_image'] ?? '';

        $stmt = $this->db->prepare(
            "INSERT INTO products (product_name, price, stock, details, category, subcategory, date_added, product_image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sdisssss', $name, $price, $stock, $details, $cat, $sub, $date, $img);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function update(int $id, array $d): bool
    {
        $name    = $d['product_name'];
        $price   = (float)$d['price'];
        $stock   = (int)($d['stock'] ?? 0);
        $details = $d['details'] ?? '';
        $cat     = $d['category'];
        $sub     = $d['subcategory'];
        $img     = $d['product_image'] ?? '';

        $stmt = $this->db->prepare(
            "UPDATE products SET product_name=?, price=?, stock=?, details=?, category=?, subcategory=?, product_image=? WHERE id=?"
        );
        $stmt->bind_param('sdissssi', $name, $price, $stock, $details, $cat, $sub, $img, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function decrementStock(int $id, int $qty): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
        );
        $stmt->bind_param('iii', $qty, $id, $qty);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }
}