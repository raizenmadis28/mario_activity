<?php
namespace App\Models;
use App\Core\Model;

class Customer extends Model
{
    protected string $table = 'customers';

    public function create(array $d): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO customers (name, email, address, username, password) VALUES (?,?,?,?,?)"
        );
        $stmt->bind_param('sssss',
            $d['name'], $d['email'], $d['address'], $d['username'], $d['password']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}