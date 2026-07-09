<?php
// src/controllers/BuyerController.php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Review.php';

class BuyerController {
    private $orderModel, $reviewModel, $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->orderModel = new Order($pdo);
        $this->reviewModel = new Review($pdo);
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['buyer', 'admin'])) {
            die("Akses hanya untuk pembeli");
        }
    }

    public function myOrders() {
        $orders = $this->orderModel->getByBuyerWithItems($_SESSION['user_id']);
        foreach ($orders as &$order) {
            foreach ($order['items'] as &$item) {
                $item['has_review'] = $this->reviewModel->hasReviewed($item['product_id'], $_SESSION['user_id'], $order['id']);
            }
        }
        include __DIR__ . '/../views/buyer/my_orders.php';
    }

    public function downloadProduct($orderItemId) {
        header("Location: index.php?url=order/download/$orderItemId");
        exit;
    }
}
?>