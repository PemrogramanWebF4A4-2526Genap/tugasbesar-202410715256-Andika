<?php
// src/controllers/CartController.php

require_once __DIR__ . '/../models/Product.php';

class CartController {
    private $productModel;

    public function __construct($pdo) {
        $this->productModel = new Product($pdo);
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    }

    public function index() {
        $cartItems = [];
        $total = 0;
        foreach ($_SESSION['cart'] as $productId => $qty) {
            $product = $this->productModel->findById($productId);
            if ($product && $product['status'] == 'active') {
                $cartItems[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'cover' => $product['cover_image'],
                    'quantity' => $qty,
                    'subtotal' => $product['price'] * $qty
                ];
                $total += $product['price'] * $qty;
            }
        }
        include __DIR__ . '/../views/buyer/cart.php';
    }

    public function add() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $id = (int)$_POST['product_id'];
            $product = $this->productModel->findById($id);
            if (!$product || $product['status'] !== 'active') {
                echo json_encode(['success' => false, 'message' => 'Produk tidak tersedia']);
                exit;
            }
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
            echo json_encode(['success' => true, 'totalItems' => array_sum($_SESSION['cart'])]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function update() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
            $id = (int)$_POST['product_id'];
            $qty = (int)$_POST['quantity'];
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id] = $qty;
            }
            echo json_encode(['success' => true, 'totalItems' => array_sum($_SESSION['cart'])]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function remove() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            unset($_SESSION['cart'][(int)$_POST['product_id']]);
            echo json_encode(['success' => true, 'totalItems' => array_sum($_SESSION['cart'])]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function addAndRedirect($productId) {
        $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
        header("Location: index.php?url=checkout/index");
        exit;
    }
}
?>