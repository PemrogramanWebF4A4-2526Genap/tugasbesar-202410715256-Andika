<?php
// src/controllers/SellerController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';

class SellerController {
    private $productModel, $orderModel, $categoryModel, $userModel, $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new Product($pdo);
        $this->orderModel = new Order($pdo);
        $this->categoryModel = new Category($pdo);
        $this->userModel = new User($pdo);
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['seller', 'admin'])) {
            die("Akses hanya untuk seller");
        }
    }

    public function dashboard() {
        $products = $this->productModel->getBySeller($_SESSION['user_id']);
        $totalSales = $this->orderModel->getTotalSalesBySeller($_SESSION['user_id']);
        $totalOrders = $this->orderModel->getOrderCountBySeller($_SESSION['user_id']);
        $recentOrders = $this->orderModel->getRecentOrdersBySeller($_SESSION['user_id'], 5);
        include __DIR__ . '/../views/seller/dashboard.php';
    }

    public function addProductForm() {
        $categories = $this->categoryModel->getAll();
        $errors = $_SESSION['product_errors'] ?? [];
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['product_errors'], $_SESSION['old_input']);
        include __DIR__ . '/../views/seller/add_product.php';
    }

    public function addProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=seller/dashboard");
            exit;
        }
        $errors = [];
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "File produk wajib diupload.";
        } else {
            if ($_FILES['file']['size'] > 30 * 1024 * 1024) $errors[] = "File maksimal 30MB.";
        }
        if (!isset($_FILES['cover']) || $_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Cover wajib diupload.";
        } else {
            if ($_FILES['cover']['size'] > 5 * 1024 * 1024) $errors[] = "Cover maksimal 5MB.";
            $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $errors[] = "Cover harus gambar.";
        }
        $category_id = $_POST['category_id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        if (empty($category_id)) $errors[] = "Kategori harus dipilih.";
        if (empty($name)) $errors[] = "Nama produk harus diisi.";
        if (strlen($name) < 3) $errors[] = "Nama minimal 3 karakter.";
        if ($price <= 0) $errors[] = "Harga harus lebih dari 0.";
        if (!empty($errors)) {
            $_SESSION['product_errors'] = $errors;
            $_SESSION['old_input'] = ['name' => $name, 'description' => $description, 'price' => $price, 'category_id' => $category_id];
            header("Location: index.php?url=seller/addProductForm");
            exit;
        }
        $uploadDir = __DIR__ . '/../../public/assets/uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileExt = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $filePath = time() . '_' . uniqid() . '.' . $fileExt;
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $filePath);
        $coverExt = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $coverPath = time() . '_cover_' . uniqid() . '.' . $coverExt;
        move_uploaded_file($_FILES['cover']['tmp_name'], $uploadDir . $coverPath);
        $this->productModel->create($_SESSION['user_id'], $category_id, $name, $description, $price, $filePath, $coverPath);
        unset($_SESSION['old_input']);
        header("Location: index.php?url=seller/dashboard");
        exit;
    }

    public function editProductForm($id) {
        $product = $this->productModel->findById($id);
        if (!$product || $product['seller_id'] != $_SESSION['user_id']) die("Bukan produk Anda");
        $categories = $this->categoryModel->getAll();
        include __DIR__ . '/../views/seller/edit_product.php';
    }

    public function editProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = $_POST['category_id'] ?? '';
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
            if (empty($category_id) || empty($name) || $price <= 0) die("Data tidak valid.");
            $this->productModel->update($id, $category_id, $name, $description, $price);
            header("Location: index.php?url=seller/dashboard");
            exit;
        }
    }

    public function deleteProduct($id) {
        $this->productModel->delete($id);
        header("Location: index.php?url=seller/dashboard");
        exit;
    }

    public function orders() {
        $orders = $this->orderModel->getBySellerWithItems($_SESSION['user_id']);
        include __DIR__ . '/../views/seller/orders.php';
    }

    public function confirmOrder($orderId) {
        $this->orderModel->updateStatus($orderId, 'paid');
        $order = $this->orderModel->findById($orderId);
        if ($order) {
            $buyer = $this->userModel->findById($order['buyer_id']);
            if ($buyer) {
                $notif = new Notification($this->pdo);
                $notif->create($buyer['id'], 'payment_confirmed', 'Pembayaran Dikonfirmasi', "Pembayaran order #$orderId telah dikonfirmasi. Silakan download produk.", 'index.php?url=buyer/myOrders');
                $email = new EmailHelper();
                $email->send($buyer['email'], $buyer['name'], 'Pembayaran Dikonfirmasi', EmailHelper::getPaymentConfirmedTemplate($orderId));
            }
        }
        header("Location: index.php?url=seller/orders");
        exit;
    }
}
?>