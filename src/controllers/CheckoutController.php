<?php
// src/controllers/CheckoutController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';

class CheckoutController {
    private $productModel, $orderModel, $orderItemModel, $userModel, $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new Product($pdo);
        $this->orderModel = new Order($pdo);
        $this->orderItemModel = new OrderItem($pdo);
        $this->userModel = new User($pdo);
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/loginForm");
            exit;
        }
    }

    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header("Location: index.php?url=cart/index");
            exit;
        }
        $items = [];
        $subtotal = 0;
        foreach ($cart as $productId => $qty) {
            $product = $this->productModel->findById($productId);
            if ($product && $product['status'] == 'active') {
                $items[] = ['id' => $product['id'], 'name' => $product['name'], 'price' => $product['price'], 'quantity' => $qty, 'subtotal' => $product['price'] * $qty];
                $subtotal += $product['price'] * $qty;
            }
        }
        $shippingCost = 0;
        $total = $subtotal;
        include __DIR__ . '/../views/buyer/checkout.php';
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=cart/index");
            exit;
        }
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header("Location: index.php?url=cart/index");
            exit;
        }
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $postal = trim($_POST['postal_code'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'bank_transfer';
        $shippingCost = (int)($_POST['shipping_cost'] ?? 0);
        if (empty($address) || empty($city)) {
            $_SESSION['checkout_error'] = "Alamat dan kota harus diisi.";
            header("Location: index.php?url=checkout/index");
            exit;
        }
        $subtotal = 0;
        $orderItemsData = [];
        foreach ($cart as $productId => $qty) {
            $product = $this->productModel->findById($productId);
            if ($product && $product['status'] == 'active') {
                $subtotal += $product['price'] * $qty;
                $orderItemsData[] = ['product_id' => $productId, 'quantity' => $qty, 'price' => $product['price']];
            }
        }
        $totalAmount = $subtotal + $shippingCost;
        $orderId = $this->orderModel->createOrder($_SESSION['user_id'], $totalAmount, $address, $city, $postal, $paymentMethod, $shippingCost);
        foreach ($orderItemsData as $item) {
            $this->orderItemModel->create($orderId, $item['product_id'], $item['quantity'], $item['price']);
        }
        $notifModel = new Notification($this->pdo);
        $notifModel->create($_SESSION['user_id'], 'order_created', 'Pesanan Diterima', "Pesanan #$orderId berhasil dibuat. Total Rp " . number_format($totalAmount,0,',','.'), 'index.php?url=buyer/myOrders');
        $emailHelper = new EmailHelper();
        $buyer = $this->userModel->findById($_SESSION['user_id']);
        $emailHelper->send($buyer['email'], $buyer['name'], 'Pesanan Diterima', EmailHelper::getOrderCreatedTemplate($orderId, $totalAmount));
        $sellerIds = [];
        foreach ($orderItemsData as $item) {
            $product = $this->productModel->findById($item['product_id']);
            if ($product && !in_array($product['seller_id'], $sellerIds)) $sellerIds[] = $product['seller_id'];
        }
        foreach ($sellerIds as $sellerId) {
            $notifModel->create($sellerId, 'new_order', 'Pesanan Baru', "Ada pesanan baru #$orderId untuk produk Anda.", 'index.php?url=seller/orders');
        }
        unset($_SESSION['cart']);
        header("Location: index.php?url=checkout/success&order_id=$orderId");
        exit;
    }

    public function success() {
        $orderId = $_GET['order_id'] ?? 0;
        $order = $this->orderModel->findById($orderId);
        if (!$order || $order['buyer_id'] != $_SESSION['user_id']) die("Order tidak ditemukan.");
        $items = $this->orderItemModel->getByOrderId($orderId);
        include __DIR__ . '/../views/buyer/checkout_success.php';
    }
}
?>