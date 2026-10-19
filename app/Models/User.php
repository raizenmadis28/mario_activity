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
        $r = $stmt->num_rows > 0;
        $stmt->close();
        return $r;
    }

    /**
     * Check kung ang username/email ay ginagamit na ng IBANG user (hindi kasama ang sarili).
     */
    public function existsExcept(string $username, string $email, int $excludeId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE (username=? OR email=?) AND id != ? LIMIT 1");
        $stmt->bind_param('ssi', $username, $email, $excludeId);
        $stmt->execute();
        $stmt->store_result();
        $r = $stmt->num_rows > 0;
        $stmt->close();
        return $r;
    }

    public function allByRole(string $role): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role=? ORDER BY id DESC");
        $stmt->bind_param('s', $role);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public function create(array $d): int|false
    {
        $username = $d['username'];
        $email    = $d['email'];
        $password = $d['password'];
        $role     = $d['role'];
        $fullName = $d['full_name']     ?? '';
        $address  = $d['address']       ?? '';
        $image    = $d['profile_image'] ?? '';

        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password, role, full_name, address, profile_image)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sssssss',
            $username, $email, $password, $role, $fullName, $address, $image
        );
        $ok = $stmt->execute();
        $id = $ok ? $stmt->insert_id : false;
        $stmt->close();
        return $id;
    }

    public function update(int $id, array $d): bool
    {
        $username = $d['username'];
        $email    = $d['email'];
        $role     = $d['role'];
        $fullName = $d['full_name']     ?? '';
        $address  = $d['address']       ?? '';
        $image    = $d['profile_image'] ?? '';

        $stmt = $this->db->prepare(
            "UPDATE users SET username=?, email=?, role=?, full_name=?, address=?, profile_image=? WHERE id=?"
        );
        $stmt->bind_param('ssssssi',
            $username, $email, $role, $fullName, $address, $image, $id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteByRole(int $id, string $role): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id=? AND role=?");
        $stmt->bind_param('is', $id, $role);
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

    /**
     * Update password by ID (para sa profile edit).
     */
    public function updatePasswordById(int $id, string $hash): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param('si', $hash, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}