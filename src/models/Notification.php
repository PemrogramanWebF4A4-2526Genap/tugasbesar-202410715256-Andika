<?php
// src/models/Notification.php

class Notification {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Buat notifikasi baru
    public function create($userId, $type, $title, $message, $link = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, link, is_read, created_at)
            VALUES (?, ?, ?, ?, ?, 0, NOW())
        ");
        return $stmt->execute([$userId, $type, $title, $message, $link]);
    }

    // Ambil notifikasi user (dengan limit & offset)
    public function getByUser($userId, $limit = 10, $offset = 0, $onlyUnread = false) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT * FROM notifications WHERE user_id = ?";
        if ($onlyUnread) $sql .= " AND is_read = 0";
        $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hitung notifikasi belum dibaca
    public function countUnread($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    // Tandai notifikasi sebagai dibaca
    public function markAsRead($id, $userId) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    // Tandai semua notifikasi user sebagai dibaca
    public function markAllAsRead($userId) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}