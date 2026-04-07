<?php
// app/models/UserModel.php

class UserModel extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=:e LIMIT 1");
        $stmt->execute([':e' => $email]);
        return $stmt->fetch();
    }

    public function create(string $name, string $email, string $password, string $role = 'user'): int
    {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role) VALUES (:n,:e,:p,:r)"
        );
        $stmt->execute([':n'=>$name,':e'=>$email,':p'=>$hashed,':r'=>$role]);
        return (int) $this->db->lastInsertId();
    }

    public function verifyPassword(string $plain, string $hashed): bool
    {
        return password_verify($plain, $hashed);
    }

    public function updateProfile(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET name=:name, phone=:phone WHERE id=:id"
        );
        return $stmt->execute([':name'=>$data['name'],':phone'=>$data['phone'],':id'=>$id]);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password=:p WHERE id=:id");
        return $stmt->execute([':p'=>$hashed,':id'=>$id]);
    }

    public function updateAvatar(int $id, string $filename): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET avatar=:a WHERE id=:id");
        return $stmt->execute([':a'=>$filename,':id'=>$id]);
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->query(
            "SELECT u.id, u.name, u.email, u.phone, u.role, u.is_active, u.created_at,
                    COUNT(DISTINCT o.id) AS order_count
             FROM users u
             LEFT JOIN orders o ON o.user_id = u.id
             GROUP BY u.id ORDER BY u.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function toggleActive(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET is_active = NOT is_active WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }

    // ── Addresses ─────────────────────────────────────────────────

    public function getAddresses(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM user_addresses WHERE user_id=:uid ORDER BY is_primary DESC, id DESC");
        $stmt->execute([':uid'=>$userId]);
        return $stmt->fetchAll();
    }

    public function getPrimaryAddress(int $userId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user_addresses WHERE user_id=:uid AND is_primary=1 LIMIT 1"
        );
        $stmt->execute([':uid'=>$userId]);
        return $stmt->fetch();
    }

    public function getAddress(int $addressId, int $userId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM user_addresses WHERE id=:id AND user_id=:uid");
        $stmt->execute([':id'=>$addressId,':uid'=>$userId]);
        return $stmt->fetch();
    }

    public function addAddress(int $userId, array $data): int
    {
        if (!empty($data['is_primary'])) {
            $this->db->prepare("UPDATE user_addresses SET is_primary=0 WHERE user_id=:uid")
                ->execute([':uid'=>$userId]);
        }
        $stmt = $this->db->prepare(
            "INSERT INTO user_addresses (user_id,label,recipient,phone,address,city,province,postal_code,is_primary)
             VALUES (:uid,:lbl,:rec,:phone,:addr,:city,:prov,:post,:prim)"
        );
        $stmt->execute([
            ':uid'=>$userId,             ':lbl'=>$data['label'],
            ':rec'=>$data['recipient'],  ':phone'=>$data['phone'],
            ':addr'=>$data['address'],   ':city'=>$data['city'],
            ':prov'=>$data['province'],  ':post'=>$data['postal_code'],
            ':prim'=>!empty($data['is_primary']) ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateAddress(int $addressId, int $userId, array $data): bool
    {
        if (!empty($data['is_primary'])) {
            $this->db->prepare("UPDATE user_addresses SET is_primary=0 WHERE user_id=:uid")
                ->execute([':uid'=>$userId]);
        }
        $stmt = $this->db->prepare(
            "UPDATE user_addresses SET label=:lbl, recipient=:rec, phone=:phone,
             address=:addr, city=:city, province=:prov, postal_code=:post, is_primary=:prim
             WHERE id=:id AND user_id=:uid"
        );
        return $stmt->execute([
            ':lbl'=>$data['label'],      ':rec'=>$data['recipient'],
            ':phone'=>$data['phone'],    ':addr'=>$data['address'],
            ':city'=>$data['city'],      ':prov'=>$data['province'],
            ':post'=>$data['postal_code'],':prim'=>!empty($data['is_primary'])?1:0,
            ':id'=>$addressId,           ':uid'=>$userId,
        ]);
    }

    public function deleteAddress(int $addressId, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user_addresses WHERE id=:id AND user_id=:uid");
        return $stmt->execute([':id'=>$addressId,':uid'=>$userId]);
    }
}
