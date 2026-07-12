<?php
// src/controllers/ReviewController.php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Review.php';

class ReviewController {
    private $orderModel, $productModel, $reviewModel, $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->orderModel = new Order($pdo);
        $this->productModel = new Product($pdo);
        $this->reviewModel = new Review($pdo);
    }

    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=auth/loginForm');
            exit;
        }
    }

    public function form($orderId, $productId) {
        $this->checkLogin();
        $order = $this->orderModel->getOrderWithItems($orderId);
        if (!$order || $order['buyer_id'] != $_SESSION['user_id'] || $order['status'] != 'paid') {
            die("Anda tidak dapat mereview produk ini.");
        }
        $found = false;
        foreach ($order['items'] as $item) {
            if ($item['product_id'] == $productId) { $found = true; break; }
        }
        if (!$found) die("Produk tidak ditemukan dalam pesanan.");
        if ($this->reviewModel->hasReviewed($productId, $_SESSION['user_id'], $orderId)) {
            die("Anda sudah memberikan review.");
        }
        $product = $this->productModel->findById($productId);
        include __DIR__ . '/../views/buyer/review_form.php';
    }

    public function submit() {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=home");
            exit;
        }
        $orderId = $_POST['order_id'] ?? 0;
        $productId = $_POST['product_id'] ?? 0;
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $photo = null;
        if ($rating < 1 || $rating > 5) die("Rating harus 1-5.");
        $order = $this->orderModel->findById($orderId);
        if (!$order || $order['buyer_id'] != $_SESSION['user_id'] || $order['status'] != 'paid') die("Akses ditolak.");
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ? AND product_id = ?");
        $stmt->execute([$orderId, $productId]);
        if ($stmt->fetchColumn() == 0) die("Produk tidak valid.");
        if ($this->reviewModel->hasReviewed($productId, $_SESSION['user_id'], $orderId)) die("Anda sudah mereview.");
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);
            if (in_array($mime, $allowed)) {
                $uploadDir = __DIR__ . '/../../public/assets/uploads/reviews/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo = time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photo);
            }
        }
        $this->reviewModel->create($productId, $_SESSION['user_id'], $orderId, $rating, $comment, $photo);
        header("Location: index.php?url=product/detail/$productId&review=success");
        exit;
    }

    public function productReviews($productId) {
        $reviews = $this->reviewModel->getByProduct($productId);
        $avgRating = $this->reviewModel->getAverageRating($productId);
        header('Content-Type: application/json');
        echo json_encode(['average' => $avgRating, 'reviews' => $reviews]);
        exit;
    }
}
?>