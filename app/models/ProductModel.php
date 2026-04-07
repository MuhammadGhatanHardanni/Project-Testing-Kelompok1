<?php
// app/models/ProductModel.php — DailyMart v3.0

class ProductModel extends Model
{
    protected string $table = 'products';

    public function getAll(string $search = '', int $categoryId = 0, string $sort = 'newest'): array
    {
        $sql = "SELECT p.*, p.image_url AS image, c.name AS category_name,
                       COALESCE(AVG(r.rating),0) AS avg_rating,
                       COUNT(r.id) AS review_count
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_reviews r ON r.product_id = p.id
                WHERE p.is_active = 1";
        $params = [];
        if ($search !== '') { $sql .= " AND p.name LIKE :search"; $params[':search'] = '%'.$search.'%'; }
        if ($categoryId > 0){ $sql .= " AND p.category_id = :cat_id"; $params[':cat_id'] = $categoryId; }
        $sql .= " GROUP BY p.id";
        $sql .= match($sort) {
            'price_asc'  => " ORDER BY p.price ASC",
            'price_desc' => " ORDER BY p.price DESC",
            'rating'     => " ORDER BY avg_rating DESC, p.created_at DESC",
            'popular'    => " ORDER BY p.sold_count DESC",
            default      => " ORDER BY p.created_at DESC",
        };
        $stmt = $this->db->prepare($sql); $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, p.image_url AS image, c.name AS category_name,
                    COALESCE(AVG(r.rating),0) AS avg_rating, COUNT(r.id) AS review_count
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN product_reviews r ON r.product_id = p.id
             WHERE p.id = :id GROUP BY p.id LIMIT 1"
        );
        $stmt->execute([':id' => $id]); return $stmt->fetch();
    }

    public function getFeatured(int $limit = 8): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, p.image_url AS image, c.name AS category_name,
                    COALESCE(AVG(r.rating),0) AS avg_rating, COUNT(r.id) AS review_count
             FROM products p LEFT JOIN categories c ON p.category_id=c.id
             LEFT JOIN product_reviews r ON r.product_id=p.id
             WHERE p.is_featured=1 AND p.is_active=1 AND p.stock>0
             GROUP BY p.id ORDER BY p.sold_count DESC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT); $stmt->execute(); return $stmt->fetchAll();
    }

    public function getDiscounted(int $limit = 8): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, p.image_url AS image, c.name AS category_name FROM products p
             LEFT JOIN categories c ON p.category_id=c.id
             WHERE p.is_active=1 AND p.original_price IS NOT NULL AND p.stock>0
             ORDER BY (p.original_price-p.price) DESC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT); $stmt->execute(); return $stmt->fetchAll();
    }

    public function getPopular(int $limit = 8): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, p.image_url AS image, c.name AS category_name,
                    COALESCE(AVG(r.rating),0) AS avg_rating, COUNT(r.id) AS review_count
             FROM products p LEFT JOIN categories c ON p.category_id=c.id
             LEFT JOIN product_reviews r ON r.product_id=p.id
             WHERE p.is_active=1 AND p.stock>0
             GROUP BY p.id ORDER BY p.sold_count DESC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT); $stmt->execute(); return $stmt->fetchAll();
    }

    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, p.image_url AS image, c.name AS category_name FROM products p
             LEFT JOIN categories c ON p.category_id=c.id
             WHERE p.category_id=:cat AND p.id!=:pid AND p.is_active=1
             ORDER BY RAND() LIMIT :lim"
        );
        $stmt->bindValue(':cat',$categoryId,PDO::PARAM_INT);
        $stmt->bindValue(':pid',$productId, PDO::PARAM_INT);
        $stmt->bindValue(':lim',$limit,     PDO::PARAM_INT);
        $stmt->execute(); return $stmt->fetchAll();
    }

    public function getReviews(int $productId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, u.name AS user_name, u.avatar FROM product_reviews r
             JOIN users u ON r.user_id=u.id
             WHERE r.product_id=:pid ORDER BY r.created_at DESC"
        );
        $stmt->execute([':pid'=>$productId]); return $stmt->fetchAll();
    }

    public function addReview(int $productId, int $userId, int $orderId, int $rating, string $comment): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO product_reviews (product_id,user_id,order_id,rating,comment) VALUES (:pid,:uid,:oid,:r,:c)"
            );
            $ok = $stmt->execute([':pid'=>$productId,':uid'=>$userId,':oid'=>$orderId,':r'=>$rating,':c'=>$comment]);
            if ($ok) {
                $this->db->prepare("UPDATE order_items SET is_reviewed=1 WHERE order_id=:oid AND product_id=:pid")
                    ->execute([':oid'=>$orderId,':pid'=>$productId]);
                $this->db->prepare("UPDATE products SET sold_count=sold_count+0 WHERE id=:pid")
                    ->execute([':pid'=>$productId]);
            }
            return $ok;
        } catch (PDOException) { return false; }
    }

    public function userCanReview(int $userId, int $productId, int $orderId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT oi.id FROM order_items oi JOIN orders o ON oi.order_id=o.id
             WHERE o.user_id=:uid AND oi.product_id=:pid AND o.id=:oid
               AND o.status='completed' AND oi.is_reviewed=0 LIMIT 1"
        );
        $stmt->execute([':uid'=>$userId,':pid'=>$productId,':oid'=>$orderId]);
        return (bool) $stmt->fetch();
    }

    public function getLowStock(int $threshold = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, p.image_url AS image, c.name AS category_name FROM products p
             LEFT JOIN categories c ON p.category_id=c.id
             WHERE p.stock<=:t AND p.is_active=1 ORDER BY p.stock ASC"
        );
        $stmt->execute([':t'=>$threshold]); return $stmt->fetchAll();
    }

    public function getBestSellers(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.name, p.id, p.sold_count, p.image_url AS image,
                    SUM(oi.quantity) AS total_qty, SUM(oi.subtotal) AS revenue
             FROM order_items oi JOIN products p ON oi.product_id=p.id
             GROUP BY oi.product_id ORDER BY total_qty DESC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT); $stmt->execute(); return $stmt->fetchAll();
    }

    public function getSalesStats(): array
    {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month,
                    COUNT(*) AS orders, SUM(total_price) AS revenue
             FROM orders WHERE status!='cancelled'
             GROUP BY month ORDER BY month DESC LIMIT 6"
        );
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO products (category_id,name,description,price,original_price,stock,unit,image_url,is_featured,is_active,weight)
             VALUES (:cid,:name,:desc,:price,:oprice,:stock,:unit,:image,:featured,:active,:weight)"
        );
        $stmt->execute([
            ':cid'=>$data['category_id']?:null,  ':name'=>$data['name'],
            ':desc'=>$data['description'],        ':price'=>$data['price'],
            ':oprice'=>$data['original_price']?:null, ':stock'=>$data['stock'],
            ':unit'=>$data['unit']??'pcs',        ':image'=>$data['image_url']??null,
            ':featured'=>$data['is_featured']??0, ':active'=>$data['is_active']??1,
            ':weight'=>$data['weight']??0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE products SET category_id=:cid,name=:name,description=:desc,
             price=:price,original_price=:oprice,stock=:stock,unit=:unit,
             image_url=:image,is_featured=:featured,is_active=:active,weight=:weight WHERE id=:id"
        );
        return $stmt->execute([
            ':cid'=>$data['category_id']?:null,   ':name'=>$data['name'],
            ':desc'=>$data['description'],         ':price'=>$data['price'],
            ':oprice'=>$data['original_price']?:null,':stock'=>(int)$data['stock'],
            ':unit'=>$data['unit']??'pcs',         ':image'=>$data['image_url']??null,
            ':featured'=>$data['is_featured']??0,  ':active'=>$data['is_active']??1,
            ':weight'=>$data['weight']??0,         ':id'=>$id,
        ]);
    }

    public function decreaseStock(int $id, int $quantity): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET stock=stock-:qty,sold_count=sold_count+:qty WHERE id=:id");
        return $stmt->execute([':qty'=>$quantity,':id'=>$id]);
    }

    public function hasStock(int $id, int $quantity): bool
    {
        $stmt = $this->db->prepare("SELECT stock FROM products WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$id]); $row=$stmt->fetch();
        return $row && $row['stock'] >= $quantity;
    }
}
