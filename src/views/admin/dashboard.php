<?php
// src/views/admin/dashboard.php
$users = $users ?? [];
$categories = $categories ?? [];
$products = $products ?? [];
$orders = $orders ?? [];
$chartData = $chartData ?? ['labels' => [], 'values' => []];
include __DIR__ . '/../templates/header.php';
?>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Total Users</h6>
                        <h2 class="display-6 fw-bold"><?= count($users) ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Kategori</h6>
                        <h2 class="display-6 fw-bold"><?= count($categories) ?></h2>
                    </div>
                    <i class="fas fa-tags fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info h-100 shadow-sm">
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
        <div class="card text-white bg-warning h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Total Pesanan</h6>
                        <h2 class="display-6 fw-bold"><?= count($orders) ?></h2>
                    </div>
                    <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Penjualan 7 Hari Terakhir -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold">
        <i class="fas fa-chart-line me-2"></i> Grafik Penjualan (7 Hari Terakhir)
    </div>
    <div class="card-body">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

<!-- Pesanan Terbaru -->
<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">
        <i class="fas fa-clock me-2"></i> Pesanan Terbaru
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>ID</th><th>Pembeli</th><th>Total</th><th>Status</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                    <?php $recentOrders = array_slice($orders, 0, 5); ?>
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="5" class="text-center py-3">Belum ada pesanan</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['buyer_name'] ?? '-') ?></td>
                            <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= $order['status'] == 'paid' ? 'bg-success' : ($order['status'] == 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($order['order_date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartData['labels']) ?>,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: <?= json_encode($chartData['values']) ?>,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>