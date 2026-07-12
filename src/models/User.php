<?php
// src/models/User.php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Cari user berdasarkan email
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cari user berdasarkan ID
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buat user baru
    public function create($name, $email, $password, $role = 'buyer') {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $hashed, $role]);
    }

    // Ambil semua user
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update role user
    public function updateRole($id, $role) {
        $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    // Hapus user
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Cek apakah email sudah terdaftar
    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    // Remember token
    public function updateRememberToken($userId, $token) {
        $stmt = $this->pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        return $stmt->execute([$token, $userId]);
    }

    public function findByRememberToken($token) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function clearRememberToken($userId) {
        $stmt = $this->pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    // Rating toko untuk seller
    public function getSellerRating($sellerId) {
        $stmt = $this->pdo->prepare("
            SELECT AVG(r.rating) as avg_rating, COUNT(r.id) as total_reviews
            FROM reviews r
            JOIN products p ON r.product_id = p.id
            WHERE p.seller_id = ?
        ");
        $stmt->execute([$sellerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Pagination dan pencarian user untuk admin
    public function getAllPaginated($limit, $offset, $search = '') {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT * FROM users";
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE name LIKE ? OR email LIKE ?";
            $params = ["%$search%", "%$search%"];
        }
        $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll($search = '') {
        if (!empty($search)) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE name LIKE ? OR email LIKE ?");
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        }
        return (int)$stmt->fetchColumn();
    }
}