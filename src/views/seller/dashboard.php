<?php
// src/views/seller/dashboard.php
$products = $products ?? [];
$totalSales = $totalSales ?? 0;
$totalOrders = $totalOrders ?? 0;
$recentOrders = $recentOrders ?? [];
include __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="fw-bold"><i class="fas fa-store me-2"></i> Dashboard Penjual</h2>
    <a href="index.php?url=seller/addProductForm" class="btn btn-success">
        <i class="fas fa-plus-circle"></i> Tambah Produk Baru
    </a>
</div>

<!-- Statistik Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Total Produk</h6>
                        <h2 class="display-6 fw-bold"><?= count($products) ?></h2>
                    </div>
                    <i class="fas fa-box fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Total Penjualan</h6>
                        <h2 class="display-6 fw-bold">Rp <?= number_format($totalSales, 0, ',', '.') ?></h2>
                    </div>
                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Total Pesanan Selesai</h6>
                        <h2 class="display-6 fw-bold"><?= $totalOrders ?></h2>
                    </div>
                    <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Rata-rata per Pesanan</h6>
                        <h2 class="display-6 fw-bold">Rp <?= number_format($totalOrders ? $totalSales / $totalOrders : 0, 0, ',', '.') ?></h2>
                    </div>
                    <i class="fas fa-receipt fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daftar Produk -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="fas fa-list me-2"></i> Produk Saya
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Gambar</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr><td colspan="7" class="text-center py-4">Belum ada produk. <a href="index.php?url=seller/addProductForm">Tambah produk</a></td></tr>
                            <?php else: ?>
                                <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?= $p['id'] ?></td>
                                    <td><img src="public/assets/uploads/products/<?= htmlspecialchars($p['cover_image'] ?? 'default.jpg') ?>" width="50" height="50" style="object-fit:cover" class="rounded"></td>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                                    <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge <?= $p['status'] == 'active' ? 'bg-success' : ($p['status'] == 'pending' ? 'bg-warning' : 'bg-secondary') ?>">
                                            <?= ucfirst($p['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?url=seller/editProductForm/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <a href="index.php?url=seller/deleteProduct/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="index.php?url=seller/orders" class="btn btn-link">Lihat Semua Pesanan <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="fas fa-clock me-2"></i> Pesanan Terbaru
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentOrders)): ?>
                    <p class="text-muted text-center py-3">Belum ada pesanan masuk.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentOrders as $order): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>#<?= $order['id'] ?></strong><br>
                                        <small><?= htmlspecialchars($order['buyer_name']) ?></small><br>
                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span><br>
                                        <a href="index.php?url=seller/orders" class="btn btn-sm btn-link p-0 mt-1">Proses</a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="index.php?url=seller/orders" class="btn btn-sm btn-outline-primary">Lihat Semua Pesanan</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>