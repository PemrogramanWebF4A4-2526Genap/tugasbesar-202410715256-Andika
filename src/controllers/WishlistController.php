<?php
// src/controllers/WishlistController.php

require_once __DIR__ . '/../models/Wishlist.php';

class WishlistController {
    private $wishlistModel;

    public function __construct($pdo) {
        $this->wishlistModel = new Wishlist($pdo);
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/loginForm");
            exit;
        }
    }

    public function add($productId) {
        $this->wishlistModel->add($_SESSION['user_id'], $productId);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function remove($productId) {
        $this->wishlistModel->remove($_SESSION['user_id'], $productId);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function index() {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;
        $products = $this->wishlistModel->getByUser($_SESSION['user_id'], $limit, $offset);
        $total = $this->wishlistModel->countByUser($_SESSION['user_id']);
        include __DIR__ . '/../views/buyer/wishlist.php';
    }
}
?>