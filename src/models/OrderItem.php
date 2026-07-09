<?php
// src/models/OrderItem.php

class OrderItem {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tambah item ke order
    public function create($orderId, $productId, $quantity, $price) {
        $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$orderId, $productId, $quantity, $price]);
    }

    // Ambil semua item berdasarkan order_id (dengan data produk)
    public function getByOrderId($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, p.name as product_name, p.cover_image, p.file_path
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Untuk download (cek kepemilikan dan status paid)
    public function getDownloadableItem($orderItemId) {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, o.status, o.buyer_id, p.file_path, p.name
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            WHERE oi.id = ?
        ");
        $stmt->execute([$orderItemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Hapus item (opsional)
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE id = ?");
        return $stmt->execute([$id]);
    }
}