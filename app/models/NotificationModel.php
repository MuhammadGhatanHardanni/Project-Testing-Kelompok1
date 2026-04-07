<?php
// app/models/NotificationModel.php

class NotificationModel extends Model
{
    protected string $table = 'notifications';

    public function getByUser(int $userId, bool $unreadOnly = false): array
    {
        $sql = "SELECT * FROM notifications WHERE user_id=:uid";
        if ($unreadOnly) $sql .= " AND is_read=0";
        $sql .= " ORDER BY created_at DESC LIMIT 20";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid'=>$userId]);
        return $stmt->fetchAll();
    }

    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=:uid AND is_read=0");
        $stmt->execute([':uid'=>$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function markAllRead(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read=1 WHERE user_id=:uid");
        return $stmt->execute([':uid'=>$userId]);
    }

    public function create(int $userId, string $type, string $title, string $message, ?string $link = null): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO notifications (user_id,type,title,message,link) VALUES (:uid,:type,:title,:msg,:link)"
        );
        return $stmt->execute([':uid'=>$userId,':type'=>$type,':title'=>$title,':msg'=>$message,':link'=>$link]);
    }
}
