<?php
// src/views/templates/header.php
// Hitung jumlah item keranjang
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// Notifikasi in-app
$unreadCount = 0;
$latestNotifications = [];
if (isset($_SESSION['user_id']) && isset($pdo)) {
    require_once __DIR__ . '/../../models/Notification.php';
    $notifModel = new Notification($pdo);
    $unreadCount = $notifModel->countUnread($_SESSION['user_id']);
    $latestNotifications = $notifModel->getByUser($_SESSION['user_id'], 5, 0, false);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>DigiStore - Toko Produk Digital</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php?url=home">DigiStore</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php?url=home">Beranda</a></li>

                <!-- Admin Dropdown -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            Admin Panel
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="index.php?url=admin/dashboard">Dashboard</a></li>
                            <li><a class="dropdown-item" href="index.php?url=admin/users">Kelola User</a></li>
                            <li><a class="dropdown-item" href="index.php?url=admin/categories">Kelola Kategori</a></li>
                            <li><a class="dropdown-item" href="index.php?url=admin/pendingProducts">Pending Products</a></li>
                            <li><a class="dropdown-item" href="index.php?url=admin/orders">Semua Pesanan</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Seller Menu -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'seller'): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?url=seller/dashboard">Dashboard Penjual</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?url=seller/orders">Pesanan Masuk</a></li>
                <?php endif; ?>
            </ul>

            <!-- Search Form -->
            <form class="d-flex search-wrapper mx-auto" method="GET" action="index.php">
                <input type="hidden" name="url" value="product/search">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="Cari produk digital..." aria-label="Cari">
                    <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto">
                <!-- Cart -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="index.php?url=cart/index">
                        <i class="fas fa-shopping-cart"></i> Keranjang
                        <?php if ($cartCount > 0): ?>
                            <span class="badge-cart"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Notifications + User Menu -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="width: 320px;">
                            <?php if (empty($latestNotifications)): ?>
                                <li><span class="dropdown-item text-muted">Tidak ada notifikasi</span></li>
                            <?php else: ?>
                                <?php foreach ($latestNotifications as $notif): ?>
                                    <li>
                                        <a class="dropdown-item <?= $notif['is_read'] ? '' : 'fw-bold' ?>" href="index.php?url=notification/read/<?= $notif['id'] ?>">
                                            <div><strong><?= htmlspecialchars($notif['title']) ?></strong></div>
                                            <div><small><?= htmlspecialchars(substr($notif['message'], 0, 60)) ?>...</small></div>
                                            <div class="text-muted" style="font-size: 10px;"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="index.php?url=notification/index">Lihat Semua</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($_SESSION['role'] == 'buyer'): ?>
                                <li><a class="dropdown-item" href="index.php?url=buyer/myOrders">Pesanan Saya</a></li>
                                <li><a class="dropdown-item" href="index.php?url=wishlist/index">Wishlist</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="index.php?url=notification/index">Notifikasi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="index.php?url=auth/logout">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?url=auth/loginForm"><i class="fas fa-sign-in-alt"></i> Masuk</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2" href="index.php?url=auth/registerForm">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4"></div>