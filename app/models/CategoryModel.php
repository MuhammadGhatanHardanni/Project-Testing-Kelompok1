<?php
// app/models/CategoryModel.php

class CategoryModel extends Model
{
    protected string $table = 'categories';

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");
        return $stmt->fetchAll();
    }

    public function getAllWithCount(): array
    {
        $stmt = $this->db->query(
            "SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c LEFT JOIN products p ON p.category_id=c.id AND p.is_active=1
             GROUP BY c.id ORDER BY c.sort_order ASC"
        );
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug=:slug LIMIT 1");
        $stmt->execute([':slug'=>$slug]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $slug = $this->makeSlug($data['name']);
        $stmt = $this->db->prepare(
            "INSERT INTO categories (name,slug,icon,description,sort_order) VALUES (:n,:s,:i,:d,:o)"
        );
        $stmt->execute([':n'=>$data['name'],':s'=>$slug,':i'=>$data['icon']??'bi-grid',':d'=>$data['description']??null,':o'=>$data['sort_order']??0]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $slug = $this->makeSlug($data['name']);
        $stmt = $this->db->prepare(
            "UPDATE categories SET name=:n, slug=:s, icon=:i, description=:d, sort_order=:o WHERE id=:id"
        );
        return $stmt->execute([':n'=>$data['name'],':s'=>$slug,':i'=>$data['icon']??'bi-grid',':d'=>$data['description']??null,':o'=>$data['sort_order']??0,':id'=>$id]);
    }

    private function makeSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
}
