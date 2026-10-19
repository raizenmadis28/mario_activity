<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByLogin(string $v): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->bind_param('ss', $v, $v);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function exists(string $username, string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function create(array $d): int|false
    {
        $stmt = $this->db->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $d['username'], $d['email'], $d['password'], $d['role']);
        $ok = $stmt->execute();
        $id = $ok ? $stmt->insert_id : false;
        $stmt->close();
        return $id;
    }

    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
        $stmt->bind_param('sssi', $d['username'], $d['email'], $d['role'], $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updatePasswordByEmail(string $email, string $hash): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->bind_param('ss', $hash, $email);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}