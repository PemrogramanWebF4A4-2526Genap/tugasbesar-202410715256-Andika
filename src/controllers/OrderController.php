<?php
// src/controllers/OrderController.php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Notification.php';

class OrderController {
    private $orderModel, $orderItemModel, $paymentModel, $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->orderModel = new Order($pdo);
        $this->orderItemModel = new OrderItem($pdo);
        $this->paymentModel = new Payment($pdo);
    }

    public function uploadProofForm($orderId) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/loginForm");
            exit;
        }
        $order = $this->orderModel->getOrderWithItems($orderId);
        if (!$order || $order['buyer_id'] != $_SESSION['user_id'] || $order['status'] !== 'pending') {
            die("Akses ditolak.");
        }
        include __DIR__ . '/../views/buyer/upload_proof.php';
    }

    public function uploadProof() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header("Location: index.php?url=home");
            exit;
        }
        $orderId = $_POST['order_id'] ?? 0;
        $file = $_FILES['proof'] ?? null;
        $order = $this->orderModel->findById($orderId);
        if (!$order || $order['buyer_id'] != $_SESSION['user_id'] || $order['status'] !== 'pending') {
            die("Akses ditolak.");
        }
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            die("Gagal upload file.");
        }
        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) die("Tipe file tidak diizinkan.");
        $uploadDir = __DIR__ . '/../../public/assets/uploads/proofs/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
        $this->orderModel->updateProof($orderId, $filename);
        $payment = $this->paymentModel->getLatestByOrderId($orderId);
        if ($payment) $this->paymentModel->updateProof($payment['id'], $filename);
        else $this->paymentModel->create($orderId, $order['payment_method'], $order['total_amount'], $filename, null);
        header("Location: index.php?url=buyer/myOrders&upload=success");
        exit;
    }

    public function download($orderItemId) {
        if (!isset($_SESSION['user_id'])) die("Harus login.");
        $item = $this->orderItemModel->getDownloadableItem($orderItemId);
        if (!$item || $item['buyer_id'] != $_SESSION['user_id'] || $item['status'] !== 'paid') {
            die("Akses ditolak.");
        }
        $filePath = __DIR__ . '/../../public/assets/uploads/products/' . $item['file_path'];
        if (!file_exists($filePath)) die("File tidak ditemukan.");
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($item['file_path']) . '"');
        readfile($filePath);
        exit;
    }

    public function cancel($orderId) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/loginForm");
            exit;
        }
        $order = $this->orderModel->findById($orderId);
        if (!$order || $order['buyer_id'] != $_SESSION['user_id'] || $order['status'] !== 'pending') {
            die("Akses ditolak.");
        }
        $this->orderModel->updateStatus($orderId, 'cancelled');
        $payment = $this->paymentModel->getLatestByOrderId($orderId);
        if ($payment) $this->paymentModel->updateStatus($payment['id'], 'failed');
        header("Location: index.php?url=buyer/myOrders&cancel=success");
        exit;
    }
}
?>