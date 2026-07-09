<?php
// src/views/admin/pending_products.php
$products = $products ?? [];
include __DIR__ . '/../templates/header.php';
?>

<h2><i class="fas fa-clock"></i> Produk Menunggu Persetujuan</h2>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>ID</th><th>Nama Produk</th><th>Penjual</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="7" class="text-center py-3">Tidak ada produk pending.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['seller_name']) ?></td>
                            <td><?= htmlspecialchars($p['category_name']) ?></td>
                            <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>
                                <a href="index.php?url=admin/approveProduct/<?= $p['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                                <a href="index.php?url=admin/rejectProduct/<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tolak produk ini?')">Reject</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>