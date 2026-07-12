<?php
// src/models/Payment.php

class Payment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Catat pembayaran baru
    public function create($orderId, $paymentMethod, $amount, $proof = null, $transactionId = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO payments (order_id, payment_method, amount, proof, transaction_id, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([$orderId, $paymentMethod, $amount, $proof, $transactionId]);
    }

    // Update status payment
    public function updateStatus($paymentId, $status) {
        $allowed = ['pending', 'success', 'failed', 'refunded'];
        if (!in_array($status, $allowed)) return false;
        $stmt = $this->pdo->prepare("UPDATE payments SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $paymentId]);
    }

    // Ambil semua payment berdasarkan order
    public function getByOrderId($orderId) {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY payment_date DESC");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil payment terbaru untuk order
    public function getLatestByOrderId($orderId) {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY payment_date DESC LIMIT 1");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update bukti pembayaran (manual)
    public function updateProof($paymentId, $proofFilename) {
        $stmt = $this->pdo->prepare("UPDATE payments SET proof = ? WHERE id = ?");
        return $stmt->execute([$proofFilename, $paymentId]);
    }
}