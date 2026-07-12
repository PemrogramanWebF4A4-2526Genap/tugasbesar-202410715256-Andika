<?php
// src/views/seller/orders.php
$orders = $orders ?? [];
include __DIR__ . '/../templates/header.php';
?>

<h2><i class="fas fa-inbox"></i> Pesanan Masuk (Produk Saya)</h2>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">Belum ada pesanan masuk.</div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <strong>Pesanan #<?= $order['id'] ?></strong>
                    </div>
                    <div class="col-md-4">
                        Pembeli: <?= htmlspecialchars($order['buyer_name'] ?? '-') ?>
                    </div>
                    <div class="col-md-4 text-md-end">
                        Status: 
                        <span class="badge <?= $order['status'] == 'paid' ? 'bg-success' : ($order['status'] == 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                        - Tanggal: <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Alamat:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>, <?= $order['city'] ?> - <?= $order['postal_code'] ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Metode:</strong> <?= strtoupper(str_replace('_', ' ', $order['payment_method'] ?? '-')) ?>
                        <?php if (!empty($order['payment_proof'])): ?>
                            <br><strong>Bukti Bayar:</strong> <a href="public/assets/uploads/proofs/<?= $order['payment_proof'] ?>" target="_blank">Lihat</a>
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

                <?php if ($order['status'] == 'pending' && !empty($order['payment_proof'])): ?>
                    <div class="mt-3">
                        <a href="index.php?url=seller/confirmOrder/<?= $order['id'] ?>" class="btn btn-success" onclick="return confirm('Konfirmasi pembayaran?')">
                            <i class="fas fa-check"></i> Konfirmasi Pembayaran
                        </a>
                    </div>
                <?php elseif ($order['status'] == 'paid'): ?>
                    <div class="mt-3 text-success">
                        <i class="fas fa-check-circle"></i> Sudah dibayar dan dikonfirmasi.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>