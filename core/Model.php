<?php
// core/Model.php

abstract class Model
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a single record by primary key.
     */
    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get all records.
     */
    public function all(string $orderBy = 'id DESC'): array
    {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    /**
     * Delete record by id.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Count records.
     */
    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM `{$this->table}`");
        return (int) $stmt->fetchColumn();
    }
}
