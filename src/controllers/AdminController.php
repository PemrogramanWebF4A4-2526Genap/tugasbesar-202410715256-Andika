<?php
// src/controllers/AdminController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';

class AdminController {
    private $userModel, $categoryModel, $productModel, $orderModel, $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->categoryModel = new Category($pdo);
        $this->productModel = new Product($pdo);
        $this->orderModel = new Order($pdo);
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            die("Akses hanya untuk admin");
        }
    }

    public function dashboard() {
        $users = $this->userModel->getAll();
        $categories = $this->categoryModel->getAll();
        $products = $this->productModel->getAll();
        $orders = $this->orderModel->getAllWithItems();
        $chartData = $this->getSalesChartData();
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    private function getSalesChartData() {
        $stmt = $this->pdo->prepare("SELECT DATE(order_date) as date, SUM(total_amount) as total FROM orders WHERE status = 'paid' AND order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(order_date) ORDER BY date ASC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $labels = [];
        $values = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime($date));
            $found = false;
            foreach ($data as $row) {
                if ($row['date'] == $date) { $values[] = (float)$row['total']; $found = true; break; }
            }
            if (!$found) $values[] = 0;
        }
        return ['labels' => $labels, 'values' => $values];
    }

    public function users() {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = trim($_GET['search'] ?? '');
        $users = $this->userModel->getAllPaginated($limit, $offset, $search);
        $total = $this->userModel->countAll($search);
        $totalPages = ceil($total / $limit);
        include __DIR__ . '/../views/admin/users.php';
    }

    public function editUser($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->userModel->updateRole($id, $_POST['role'] ?? 'buyer');
            header("Location: index.php?url=admin/users");
            exit;
        }
    }

    public function deleteUser($id) {
        $this->userModel->delete($id);
        header("Location: index.php?url=admin/users");
        exit;
    }

    public function categories() {
        $categories = $this->categoryModel->getAll();
        include __DIR__ . '/../views/admin/categories.php';
    }

    public function addCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (!empty($name)) $this->categoryModel->create($name);
            header("Location: index.php?url=admin/categories");
            exit;
        }
    }

    public function editCategoryForm($id) {
        $category = $this->categoryModel->findById($id);
        if (!$category) die("Kategori tidak ditemukan");
        include __DIR__ . '/../views/admin/edit_category.php';
    }

    public function editCategory($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if (!empty($name)) $this->categoryModel->update($id, $name);
            header("Location: index.php?url=admin/categories");
            exit;
        }
    }

    public function deleteCategory($id) {
        $this->categoryModel->delete($id);
        header("Location: index.php?url=admin/categories");
        exit;
    }

    public function pendingProducts() {
        $products = $this->productModel->getPending();
        include __DIR__ . '/../views/admin/pending_products.php';
    }

    public function approveProduct($id) {
        $this->productModel->updateStatus($id, 'active');
        header("Location: index.php?url=admin/pendingProducts");
        exit;
    }

    public function rejectProduct($id) {
        $this->productModel->updateStatus($id, 'inactive');
        header("Location: index.php?url=admin/pendingProducts");
        exit;
    }

    public function orders() {
        $status = $_GET['status'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $orders = $this->orderModel->getAllWithItemsFiltered($status, $limit, $offset);
        $total = $this->orderModel->countAllFiltered($status);
        $totalPages = ceil($total / $limit);
        include __DIR__ . '/../views/admin/orders.php';
    }

    public function exportOrders() {
        $status = $_GET['status'] ?? '';
        $orders = $this->orderModel->getAllWithItemsFiltered($status, 10000, 0);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="orders_' . date('Ymd') . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Pembeli', 'Total', 'Status', 'Tanggal', 'Metode Pembayaran']);
        foreach ($orders as $order) {
            fputcsv($output, [$order['id'], $order['buyer_name'], $order['total_amount'], $order['status'], $order['order_date'], $order['payment_method']]);
        }
        fclose($output);
        exit;
    }
}
?>