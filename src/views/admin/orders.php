<?php
// src/views/admin/orders.php
$orders = $orders ?? [];
$totalPages = $totalPages ?? 1;
$page = $page ?? 1;
$statusFilter = $_GET['status'] ?? '';
include __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2><i class="fas fa-shopping-cart"></i> Semua Pesanan</h2>
    <div class="d-flex gap-2">
        <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="url" value="admin/orders">
            <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="paid" <?= $statusFilter == 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="cancelled" <?= $statusFilter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </form>
        <a href="index.php?url=admin/exportOrders&status=<?= urlencode($statusFilter) ?>" class="btn btn-success">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
</div>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">Tidak ada pesanan.</div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-3"><strong>Order #<?= $order['id'] ?></strong></div>
                    <div class="col-md-3">Pembeli: <?= htmlspecialchars($order['buyer_name'] ?? '-') ?></div>
                    <div class="col-md-3">Total: Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></div>
                    <div class="col-md-3">
                        Status: <span class="badge <?= $order['status'] == 'paid' ? 'bg-success' : ($order['status'] == 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Alamat:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>, <?= $order['city'] ?> - <?= $order['postal_code'] ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Metode:</strong> <?= strtoupper(str_replace('_', ' ', $order['payment_method'] ?? '-')) ?><br>
                        <strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                        <?php if (!empty($order['payment_proof'])): ?>
                            <br><strong>Bukti:</strong> <a href="public/assets/uploads/proofs/<?= $order['payment_proof'] ?>" target="_blank">Lihat</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr><th colspan="3" class="text-end">Total</th><th>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></th></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?url=admin/orders&page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>