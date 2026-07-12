<?php
// ============================================================
// index.php – Front Controller Digital Product Store
// ============================================================

// ---------- DEBUG (AKTIFKAN SAAT ERROR) ----------
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// ---------- SESSION ----------
session_start();

// ---------- LOAD .ENV ----------
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// ---------- KONEKSI DATABASE ----------
require_once __DIR__ . '/src/config/database.php';

// ---------- AUTO LOGIN (REMEMBER ME) ----------
require_once __DIR__ . '/src/controllers/AuthController.php';
AuthController::autoLogin($pdo);

// ---------- WEBHOOK MIDTRANS ----------
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestUri === '/webhook') {
    require_once __DIR__ . '/src/controllers/PaymentController.php';
    $paymentController = new PaymentController($pdo);
    $paymentController->webhook();
    exit;
}

// ---------- ROUTING ----------
$url = isset($_GET['url']) ? $_GET['url'] : 'home';
$url = rtrim($url, '/');
$segments = explode('/', $url);

// Ambil controller, method, parameter
$controllerName = ucfirst(array_shift($segments)) . 'Controller';
$method = array_shift($segments) ?: 'index';
$params = $segments;

// Alias 'home' ke ProductController
if ($controllerName === 'HomeController') {
    $controllerName = 'ProductController';
    $method = 'index';
}

$controllerFile = __DIR__ . '/src/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName($pdo);
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            die("Method '$method' tidak ditemukan di controller '$controllerName'");
        }
    } else {
        die("Class '$controllerName' tidak ditemukan di file '$controllerFile'");
    }
} else {
    // Fallback ke halaman produk
    require_once __DIR__ . '/src/controllers/ProductController.php';
    $fallbackController = new ProductController($pdo);
    $fallbackController->index();
}
?>