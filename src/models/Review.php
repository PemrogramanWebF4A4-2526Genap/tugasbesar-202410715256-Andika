<?php
// src/models/Review.php

class Review {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tambah review
    public function create($productId, $userId, $orderId, $rating, $comment, $photo = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO reviews (product_id, user_id, order_id, rating, comment, photo, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$productId, $userId, $orderId, $rating, $comment, $photo]);
    }

    // Cek apakah user sudah mereview produk (pada order tertentu)
    public function hasReviewed($productId, $userId, $orderId = null) {
        $sql = "SELECT COUNT(*) FROM reviews WHERE product_id = ? AND user_id = ?";
        $params = [$productId, $userId];
        if ($orderId) {
            $sql .= " AND order_id = ?";
            $params[] = $orderId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // Ambil semua review untuk produk
    public function getByProduct($productId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.name as user_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hitung rata-rata rating produk
    public function getAverageRating($productId) {
        $stmt = $this->pdo->prepare("SELECT AVG(rating) FROM reviews WHERE product_id = ?");
        $stmt->execute([$productId]);
        return round((float)$stmt->fetchColumn(), 1);
    }

    // Hapus review (admin)
    public function delete($reviewId) {
        $stmt = $this->pdo->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$reviewId]);
    }
}