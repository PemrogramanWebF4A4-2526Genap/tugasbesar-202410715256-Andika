<?php
// src/controllers/ProductController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Wishlist.php';

class ProductController {
    private $productModel, $categoryModel, $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new Product($pdo);
        $this->categoryModel = new Category($pdo);
    }

    public function index() {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;
        $products = $this->productModel->getActiveProductsPaginated($limit, $offset);
        $total = $this->productModel->countActiveProducts();
        $recommended = $this->productModel->getRecommended(8);
        $bestSellers = $this->productModel->getBestSellers(5);
        $categories = $this->categoryModel->getAll();
        include __DIR__ . '/../views/buyer/products.php';
    }

    public function search() {
        $keyword = $_GET['q'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';
        $minPrice = (int)($_GET['min_price'] ?? 0);
        $maxPrice = (int)($_GET['max_price'] ?? 0);
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;
        $products = $this->productModel->searchWithFilter($keyword, $sort, $minPrice, $maxPrice, $limit, $offset);
        $total = $this->productModel->countSearchWithFilter($keyword, $minPrice, $maxPrice);
        $categories = $this->categoryModel->getAll();
        $bestSellers = $this->productModel->getBestSellers(5);
        include __DIR__ . '/../views/buyer/products.php';
    }

    public function category($catId) {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;
        $products = $this->productModel->getByCategoryPaginated($catId, $limit, $offset);
        $total = $this->productModel->countByCategory($catId);
        $categories = $this->categoryModel->getAll();
        $bestSellers = $this->productModel->getBestSellers(5);
        include __DIR__ . '/../views/buyer/products.php';
    }

    public function detail($id) {
        $product = $this->productModel->findById($id);
        if (!$product) die("Produk tidak ditemukan.");
        $reviewModel = new Review($this->pdo);
        $reviews = $reviewModel->getByProduct($id);
        $avgRating = $reviewModel->getAverageRating($id);
        $isWishlisted = false;
        if (isset($_SESSION['user_id'])) {
            $wishlistModel = new Wishlist($this->pdo);
            $isWishlisted = $wishlistModel->isWishlisted($_SESSION['user_id'], $id);
        }
        $categories = $this->categoryModel->getAll();
        include __DIR__ . '/../views/buyer/product_detail.php';
    }
}
?>