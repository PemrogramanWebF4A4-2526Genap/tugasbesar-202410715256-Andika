<?php
// src/controllers/StoreController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';

class StoreController {
    private $productModel, $userModel;

    public function __construct($pdo) {
        $this->productModel = new Product($pdo);
        $this->userModel = new User($pdo);
    }

    public function index($sellerId) {
        $seller = $this->userModel->findById($sellerId);
        if (!$seller || !in_array($seller['role'], ['seller', 'admin'])) {
            die("Toko tidak ditemukan.");
        }
        $products = $this->productModel->getBySeller($sellerId);
        $rating = $this->userModel->getSellerRating($sellerId);
        include __DIR__ . '/../views/store/index.php';
    }
}
?>