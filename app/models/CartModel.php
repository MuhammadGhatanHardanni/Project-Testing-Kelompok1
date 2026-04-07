<?php
// app/models/CartModel.php

class CartModel extends Model
{
    protected string $table = 'cart';

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.quantity, c.product_id,
                    p.name, p.price, p.original_price, p.stock, p.image, p.unit,
                    (c.quantity * p.price) AS subtotal
             FROM cart c JOIN products p ON c.product_id=p.id
             WHERE c.user_id=:uid ORDER BY c.added_at ASC"
        );
        $stmt->execute([':uid'=>$userId]);
        return $stmt->fetchAll();
    }

    public function addItem(int $userId, int $productId, int $quantity = 1): bool
    {
        $stmt = $this->db->prepare("SELECT id, quantity FROM cart WHERE user_id=:uid AND product_id=:pid");
        $stmt->execute([':uid'=>$userId,':pid'=>$productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->db->prepare("UPDATE cart SET quantity=:qty WHERE id=:id");
            return $stmt->execute([':qty'=>$existing['quantity']+$quantity,':id'=>$existing['id']]);
        }
        $stmt = $this->db->prepare("INSERT INTO cart (user_id,product_id,quantity) VALUES (:uid,:pid,:qty)");
        return $stmt->execute([':uid'=>$userId,':pid'=>$productId,':qty'=>$quantity]);
    }

    public function updateQuantity(int $cartId, int $userId, int $quantity): bool
    {
        $stmt = $this->db->prepare("UPDATE cart SET quantity=:qty WHERE id=:id AND user_id=:uid");
        return $stmt->execute([':qty'=>$quantity,':id'=>$cartId,':uid'=>$userId]);
    }

    public function removeItem(int $cartId, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE id=:id AND user_id=:uid");
        return $stmt->execute([':id'=>$cartId,':uid'=>$userId]);
    }

    public function clearCart(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id=:uid");
        return $stmt->execute([':uid'=>$userId]);
    }

    public function getTotal(int $userId): float
    {
        $stmt = $this->db->prepare(
            "SELECT SUM(c.quantity*p.price) AS total FROM cart c
             JOIN products p ON c.product_id=p.id WHERE c.user_id=:uid"
        );
        $stmt->execute([':uid'=>$userId]);
        return (float)($stmt->fetch()['total'] ?? 0);
    }

    public function countItems(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM cart WHERE user_id=:uid");
        $stmt->execute([':uid'=>$userId]);
        return (int)$stmt->fetchColumn();
    }
}
