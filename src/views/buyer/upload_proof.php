<?php
// src/views/buyer/upload_proof.php
$order = $order ?? null;
if (!$order) die("Pesanan tidak valid.");
include __DIR__ . '/../templates/header.php';
?>

<h2><i class="fas fa-upload"></i> Upload Bukti Pembayaran</h2>
<p>Pesanan #<?= $order['id'] ?></p>

<?php if (!empty($order['items'])): ?>
    <div class="mb-3">
        <strong>Produk:</strong>
        <ul>
            <?php foreach ($order['items'] as $item): ?>
                <li><?= htmlspecialchars($item['product_name']) ?> (<?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?>)</li>
            <?php endforeach; ?>
        </ul>
        <p>Total: <strong>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong></p>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?url=order/uploadProof" enctype="multipart/form-data">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
    <div class="mb-3">
        <label>Bukti Transfer (jpg/png/pdf)</label>
        <input type="file" name="proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
    <a href="index.php?url=buyer/myOrders" class="btn btn-secondary">Batal</a>
</form>

<?php include __DIR__ . '/../templates/footer.php'; ?>