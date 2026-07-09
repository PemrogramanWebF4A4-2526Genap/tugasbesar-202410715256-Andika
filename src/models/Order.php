<?php
// src/models/Order.php
require_once __DIR__ . '/OrderItem.php';

class Order {
    private $pdo;
    private $orderItemModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->orderItemModel = new OrderItem($pdo);
    }

    // Buat order header baru
    public function createOrder($userId, $totalAmount, $shippingAddress, $city, $postalCode, $paymentMethod, $shippingCost) {
        $stmt = $this->pdo->prepare("
            INSERT INTO orders (buyer_id, total_amount, shipping_address, city, postal_code, payment_method, shipping_cost, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$userId, $totalAmount, $shippingAddress, $city, $postalCode, $paymentMethod, $shippingCost]);
        return $this->pdo->lastInsertId();
    }

    // Cari order berdasarkan ID
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil order + items
    public function getOrderWithItems($orderId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($order) {
            $order['items'] = $this->orderItemModel->getByOrderId($orderId);
        }
        return $order;
    }

    // Untuk keperluan payment (Midtrans)
    public function getOrderWithItemsForPayment($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT o.*, oi.quantity, oi.price, p.name as product_name, p.id as product_id
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($items)) return null;
        $order = $items[0];
        $order['items'] = $items;
        return $order;
    }

    // Update status order
    public function updateStatus($orderId, $status) {
        $allowed = ['pending', 'paid', 'cancelled'];
        if (!in_array($status, $allowed)) return false;
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    // Simpan bukti pembayaran manual
    public function updateProof($orderId, $proofFilename) {
        $stmt = $this->pdo->prepare("UPDATE orders SET payment_proof = ? WHERE id = ?");
        return $stmt->execute([$proofFilename, $orderId]);
    }

    // Simpan token dan URL pembayaran dari Midtrans
    public function updatePaymentToken($orderId, $token, $paymentUrl) {
        $stmt = $this->pdo->prepare("UPDATE orders SET payment_token = ?, payment_url = ? WHERE id = ?");
        return $stmt->execute([$token, $paymentUrl, $orderId]);
    }

    // ===== ADMIN: semua order + items =====
    public function getAllWithItems() {
        $stmt = $this->pdo->query("
            SELECT o.*, u.name as buyer_name
            FROM orders o
            JOIN users u ON o.buyer_id = u.id
            ORDER BY o.order_date DESC
        ");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemModel->getByOrderId($order['id']);
        }
        return $orders;
    }

    // Filter status + pagination (admin)
    public function getAllWithItemsFiltered($status = '', $limit = 10, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT o.*, u.name as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.id";
        $params = [];
        if (!empty($status)) {
            $sql .= " WHERE o.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY o.order_date DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemModel->getByOrderId($order['id']);
        }
        return $orders;
    }

    public function countAllFiltered($status = '') {
        if (!empty($status)) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM orders WHERE status = ?");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM orders");
        }
        return (int)$stmt->fetchColumn();
    }

    // ===== BUYER: pesanan milik buyer tertentu =====
    public function getByBuyerWithItems($buyerId) {
        $stmt = $this->pdo->prepare("
            SELECT o.* FROM orders o
            WHERE o.buyer_id = ?
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$buyerId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemModel->getByOrderId($order['id']);
        }
        return $orders;
    }

    // ===== SELLER: pesanan yang berisi produk seller =====
    public function getBySellerWithItems($sellerId) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT o.*, u_buyer.name as buyer_name
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            JOIN users u_buyer ON o.buyer_id = u_buyer.id
            WHERE p.seller_id = ?
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$sellerId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemModel->getByOrderId($order['id']);
        }
        return $orders;
    }

    // ===== Statistik untuk seller =====
    public function getTotalSalesBySeller($sellerId) {
        $stmt = $this->pdo->prepare("
            SELECT SUM(oi.quantity * oi.price) as total
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE p.seller_id = ? AND oi.order_id IN (SELECT id FROM orders WHERE status = 'paid')
        ");
        $stmt->execute([$sellerId]);
        return (float)$stmt->fetchColumn();
    }

    public function getOrderCountBySeller($sellerId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT o.id)
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE p.seller_id = ? AND o.status = 'paid'
        ");
        $stmt->execute([$sellerId]);
        return (int)$stmt->fetchColumn();
    }

    public function getRecentOrdersBySeller($sellerId, $limit = 5) {
        $limit = (int)$limit;
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT o.id, o.order_date, o.total_amount, u.name as buyer_name
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            JOIN users u ON o.buyer_id = u.id
            WHERE p.seller_id = ?
            ORDER BY o.order_date DESC
            LIMIT $limit
        ");
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}