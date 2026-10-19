<?php
namespace App\Models;
use App\Core\Model;

class Admin extends Model
{
    protected string $table = 'admins';

    public function create(string $username, string $hash): bool
    {
        $stmt = $this->db->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param('ss', $username, $hash);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}