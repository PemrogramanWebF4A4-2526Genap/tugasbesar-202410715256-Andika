<?php
// src/models/Product.php

class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ============================================================
    // CRUD DASAR
    // ============================================================

    /**
     * Ambil semua produk (tanpa filter)
     */
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT p.*, u.name as seller_name, c.name as category_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            JOIN categories c ON p.category_id = c.id
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil produk aktif (status = 'active')
     */
    public function getActiveProducts() {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.name as seller_name, c.name as category_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active'
            ORDER BY p.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cari produk berdasarkan keyword
     */
    public function search($keyword) {
        $kw = "%$keyword%";
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.name as seller_name, c.name as category_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$kw, $kw]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil produk berdasarkan kategori
     */
    public function getByCategory($categoryId) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.name as seller_name, c.name as category_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active' AND p.category_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil produk milik seller tertentu
     */
    public function getBySeller($sellerId) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.seller_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil produk dengan status pending (belum diapprove admin)
     */
    public function getPending() {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.name as seller_name, c.name as category_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'pending'
            ORDER BY p.created_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cari produk berdasarkan ID
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.name as seller_name, c.name as category_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah produk baru (status default pending)
     * Dilengkapi sanitasi UTF-8 untuk mencegah error charset
     */
    public function create($sellerId, $categoryId, $name, $description, $price, $filePath, $coverImage) {
        // Sanitasi input untuk karakter yang tidak valid
        $name = $this->cleanInvalidUTF8($name);
        $description = $this->cleanInvalidUTF8($description);

        $stmt = $this->pdo->prepare("
            INSERT INTO products (seller_id, category_id, name, description, price, file_path, cover_image, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([$sellerId, $categoryId, $name, $description, $price, $filePath, $coverImage]);
    }

    /**
     * Update produk (tanpa mengubah file)
     */
    public function update($id, $categoryId, $name, $description, $price) {
        // Sanitasi input untuk karakter yang tidak valid
        $name = $this->cleanInvalidUTF8($name);
        $description = $this->cleanInvalidUTF8($description);

        $stmt = $this->pdo->prepare("
            UPDATE products
            SET category_id = ?, name = ?, description = ?, price = ?
            WHERE id = ?
        ");
        return $stmt->execute([$categoryId, $name, $description, $price, $id]);
    }

    /**
     * Update status produk (active, inactive, pending)
     */
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE products SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Hapus produk
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ============================================================
    // FITUR REKOMENDASI & BEST SELLER
    // ============================================================

    public function getRecommended($limit = 8) {
        $limit = (int)$limit;
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.name as seller_name,
                   (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) as avg_rating
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.status = 'active'
            ORDER BY (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.id) DESC, p.created_at DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBestSellers($limit = 5) {
        $limit = (int)$limit;
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.name as seller_name,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.id) as total_sold
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.status = 'active'
            ORDER BY total_sold DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // PAGINATION
    // ============================================================

    public function getActiveProductsPaginated($limit, $offset) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT p.*, u.name as seller_name
                FROM products p
                JOIN users u ON p.seller_id = u.id
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC
                LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countActiveProducts() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'");
        return (int)$stmt->fetchColumn();
    }

    // ============================================================
    // SEARCH & FILTER (dengan pagination)
    // ============================================================

    public function searchWithFilter($keyword, $sort, $minPrice, $maxPrice, $limit, $offset) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT p.*, u.name as seller_name
                FROM products p
                JOIN users u ON p.seller_id = u.id
                WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)";
        $params = ["%$keyword%", "%$keyword%"];
        if ($minPrice > 0) { $sql .= " AND p.price >= ?"; $params[] = $minPrice; }
        if ($maxPrice > 0) { $sql .= " AND p.price <= ?"; $params[] = $maxPrice; }
        switch ($sort) {
            case 'price_asc': $sql .= " ORDER BY p.price ASC"; break;
            case 'price_desc': $sql .= " ORDER BY p.price DESC"; break;
            case 'best_seller': $sql .= " ORDER BY (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.id) DESC"; break;
            default: $sql .= " ORDER BY p.created_at DESC";
        }
        $sql .= " LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSearchWithFilter($keyword, $minPrice, $maxPrice) {
        $sql = "SELECT COUNT(*) FROM products p
                WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)";
        $params = ["%$keyword%", "%$keyword%"];
        if ($minPrice > 0) { $sql .= " AND p.price >= ?"; $params[] = $minPrice; }
        if ($maxPrice > 0) { $sql .= " AND p.price <= ?"; $params[] = $maxPrice; }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    // ============================================================
    // KATEGORI + PAGINATION
    // ============================================================

    public function getByCategoryPaginated($catId, $limit, $offset) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT p.*, u.name as seller_name
                FROM products p
                JOIN users u ON p.seller_id = u.id
                WHERE p.status = 'active' AND p.category_id = ?
                ORDER BY p.created_at DESC
                LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$catId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByCategory($catId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM products WHERE status = 'active' AND category_id = ?");
        $stmt->execute([$catId]);
        return (int)$stmt->fetchColumn();
    }

    // ============================================================
    // HELPER: Sanitasi UTF-8 (Mencegah Error "Incorrect string value")
    // ============================================================

    /**
     * Membersihkan karakter yang tidak valid untuk UTF-8
     * @param string $text
     * @return string
     */
    private function cleanInvalidUTF8($text) {
        if ($text === null) return '';
        // Mengganti karakter yang tidak valid dengan tanda tanya
        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }
}
?>