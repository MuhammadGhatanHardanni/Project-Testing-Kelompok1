<?php
// app/models/WishlistModel.php

class WishlistModel extends Model
{
    protected string $table = 'wishlist';

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT w.id, w.added_at, p.id AS product_id, p.name, p.price, p.original_price,
                    p.stock, p.image, p.unit, c.name AS category_name
             FROM wishlist w JOIN products p ON w.product_id=p.id
             LEFT JOIN categories c ON p.category_id=c.id
             WHERE w.user_id=:uid ORDER BY w.added_at DESC"
        );
        $stmt->execute([':uid'=>$userId]);
        return $stmt->fetchAll();
    }

    public function toggle(int $userId, int $productId): string
    {
        $stmt = $this->db->prepare("SELECT id FROM wishlist WHERE user_id=:uid AND product_id=:pid");
        $stmt->execute([':uid'=>$userId,':pid'=>$productId]);
        if ($stmt->fetch()) {
            $this->db->prepare("DELETE FROM wishlist WHERE user_id=:uid AND product_id=:pid")
                ->execute([':uid'=>$userId,':pid'=>$productId]);
            return 'removed';
        }
        $this->db->prepare("INSERT INTO wishlist (user_id,product_id) VALUES (:uid,:pid)")
            ->execute([':uid'=>$userId,':pid'=>$productId]);
        return 'added';
    }

    public function isWishlisted(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM wishlist WHERE user_id=:uid AND product_id=:pid");
        $stmt->execute([':uid'=>$userId,':pid'=>$productId]);
        return (bool)$stmt->fetch();
    }

    public function countByUser(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id=:uid");
        $stmt->execute([':uid'=>$userId]);
        return (int)$stmt->fetchColumn();
    }
}
