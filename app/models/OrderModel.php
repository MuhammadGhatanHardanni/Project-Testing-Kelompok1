<?php
// app/models/OrderModel.php

class OrderModel extends Model
{
    protected string $table = 'orders';

    public function createFromCart(int $userId, array $shippingData, array $cartItems, ?array $voucher = null): int|false
    {
        $subtotal = 0;
        foreach ($cartItems as $item) $subtotal += $item['subtotal'];

        $discount = 0;
        if ($voucher) {
            if ($voucher['type'] === 'percentage') {
                $discount = $subtotal * ($voucher['value'] / 100);
                if ($voucher['max_discount']) $discount = min($discount, $voucher['max_discount']);
            } else {
                $discount = min($voucher['value'], $subtotal);
            }
        }

        $total = max(0, $subtotal - $discount);

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "INSERT INTO orders (user_id,voucher_id,recipient_name,address,city,phone,subtotal,discount_amount,total_price,notes)
                 VALUES (:uid,:vid,:rname,:addr,:city,:phone,:sub,:disc,:total,:notes)"
            );
            $stmt->execute([
                ':uid'  =>$userId,  ':vid'  =>$voucher['id'] ?? null,
                ':rname'=>$shippingData['recipient_name'], ':addr'=>$shippingData['address'],
                ':city' =>$shippingData['city'] ?? null,  ':phone'=>$shippingData['phone'],
                ':sub'  =>$subtotal, ':disc'=>$discount,  ':total'=>$total,
                ':notes'=>$shippingData['notes'] ?? null,
            ]);
            $orderId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                "INSERT INTO order_items (order_id,product_id,product_name,price,quantity,subtotal)
                 VALUES (:oid,:pid,:pname,:price,:qty,:sub)"
            );

            $productModel = new ProductModel();
            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    ':oid'=>$orderId, ':pid'=>$item['product_id'],
                    ':pname'=>$item['name'], ':price'=>$item['price'],
                    ':qty'=>$item['quantity'], ':sub'=>$item['subtotal'],
                ]);
                $productModel->decreaseStock($item['product_id'], $item['quantity']);
            }

            // Increment voucher used count
            if ($voucher) {
                $this->db->prepare("UPDATE vouchers SET used_count=used_count+1 WHERE id=:id")
                    ->execute([':id'=>$voucher['id']]);
            }

            // Create notification
            $this->db->prepare(
                "INSERT INTO notifications (user_id,type,title,message,link) VALUES (:uid,'order',:title,:msg,'/orders')"
            )->execute([
                ':uid'=>$userId,
                ':title'=>'Pesanan Dikonfirmasi',
                ':msg'=>'Pesanan #ORD-' . date('Ymd') . '-' . str_pad($orderId,5,'0',STR_PAD_LEFT) . ' sedang diproses.',
            ]);

            $this->db->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Order error: ' . $e->getMessage());
            return false;
        }
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id=:uid ORDER BY created_at DESC");
        $stmt->execute([':uid'=>$userId]);
        return $stmt->fetchAll();
    }

    public function getAllWithUser(string $status = '', string $search = ''): array
    {
        $sql = "SELECT o.*, u.name AS user_name, u.email AS user_email
                FROM orders o LEFT JOIN users u ON o.user_id=u.id WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND o.status=:status"; $params[':status']=$status; }
        if ($search) { $sql .= " AND (u.name LIKE :s OR u.email LIKE :s2 OR o.recipient_name LIKE :s3)";
            $params[':s']=$params[':s2']=$params[':s3']='%'.$search.'%'; }
        $sql .= " ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getDetailById(int $orderId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name AS user_name, u.email AS user_email,
                    v.code AS voucher_code, v.type AS voucher_type, v.value AS voucher_value
             FROM orders o LEFT JOIN users u ON o.user_id=u.id
             LEFT JOIN vouchers v ON o.voucher_id=v.id
             WHERE o.id=:id LIMIT 1"
        );
        $stmt->execute([':id'=>$orderId]);
        $order = $stmt->fetch();
        if (!$order) return false;

        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id=:oid");
        $stmt->execute([':oid'=>$orderId]);
        $order['items'] = $stmt->fetchAll();
        return $order;
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        $allowed = ['pending','processing','shipped','completed','cancelled'];
        if (!in_array($status, $allowed)) return false;
        $stmt = $this->db->prepare("UPDATE orders SET status=:s WHERE id=:id");
        return $stmt->execute([':s'=>$status,':id'=>$orderId]);
    }

    public function countAll(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    public function getTotalRevenue(): float
    {
        return (float)$this->db->query("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE status='completed'")->fetchColumn();
    }

    public function getMonthlyStats(): array
    {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month,
                    COUNT(*) AS orders, SUM(total_price) AS revenue
             FROM orders WHERE status != 'cancelled'
             GROUP BY month ORDER BY month DESC LIMIT 6"
        );
        return $stmt->fetchAll();
    }

    public function getStatusCounts(): array
    {
        $stmt = $this->db->query("SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status");
        $result = [];
        foreach ($stmt->fetchAll() as $row) $result[$row['status']] = $row['cnt'];
        return $result;
    }

    public function getRecentOrders(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name AS user_name FROM orders o
             LEFT JOIN users u ON o.user_id=u.id
             ORDER BY o.created_at DESC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
