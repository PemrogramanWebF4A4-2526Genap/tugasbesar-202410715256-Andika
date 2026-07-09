<?php
// src/models/Wishlist.php

class Wishlist {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tambah wishlist
    public function add($userId, $productId) {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO wishlists (user_id, product_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $productId]);
    }

    // Hapus wishlist
    public function remove($userId, $productId) {
        $stmt = $this->pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$userId, $productId]);
    }

    // Cek apakah produk sudah diwishlist
    public function isWishlisted($userId, $productId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return $stmt->fetchColumn() > 0;
    }

    // Ambil daftar wishlist user (dengan pagination)
    public function getByUser($userId, $limit = 12, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT p.*, u.name as seller_name
                FROM wishlists w
                JOIN products p ON w.product_id = p.id
                JOIN users u ON p.seller_id = u.id
                WHERE w.user_id = ?
                ORDER BY w.created_at DESC
                LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Total wishlist user
    public function countByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}