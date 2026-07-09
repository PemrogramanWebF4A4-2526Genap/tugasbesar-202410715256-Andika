<?php
// src/views/buyer/my_orders.php
$orders = $orders ?? [];
include __DIR__ . '/../templates/header.php';
?>

<h2><i class="fas fa-list"></i> Pesanan Saya</h2>

<?php if (isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
    <div class="alert alert-success">Bukti pembayaran berhasil diunggah. Menunggu konfirmasi penjual.</div>
<?php endif; ?>
<?php if (isset($_GET['cancel']) && $_GET['cancel'] == 'success'): ?>
    <div class="alert alert-warning">Pesanan telah dibatalkan.</div>
<?php endif; ?>
<?php if (isset($_GET['review']) && $_GET['review'] == 'success'): ?>
    <div class="alert alert-success">Terima kasih atas review Anda.</div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">Anda belum memiliki pesanan. <a href="index.php?url=home">Belanja sekarang</a></div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <strong>Pesanan #<?= htmlspecialchars($order['id']) ?></strong>
                    - Tanggal: <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                </div>
                <div>
                    Status: 
                    <span class="badge 
                        <?= $order['status'] == 'paid' ? 'bg-success' : ($order['status'] == 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Daftar produk dalam pesanan -->
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <img src="public/assets/uploads/products/<?= htmlspecialchars($item['cover_image'] ?? 'default.jpg') ?>" width="40" class="me-2 rounded">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </td>
                                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($order['status'] == 'paid'): ?>
                                        <a href="index.php?url=order/download/<?= $item['id'] ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <?php if (isset($item['has_review']) && !$item['has_review']): ?>
                                            <a href="index.php?url=review/form/<?= $order['id'] ?>/<?= $item['product_id'] ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-star"></i> Beri Review
                                            </a>
                                        <?php elseif (isset($item['has_review']) && $item['has_review']): ?>
                                            <span class="badge bg-secondary">Sudah review</span>
                                        <?php endif; ?>
                                    <?php elseif ($order['status'] == 'pending'): ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Belum bisa download</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Ringkasan alamat & total -->
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Alamat:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>, <?= $order['city'] ?> - <?= $order['postal_code'] ?>
                        <br><strong>Metode:</strong> <?= strtoupper(str_replace('_', ' ', $order['payment_method'])) ?>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if ($order['shipping_cost'] > 0): ?>
                            <p class="mb-1">Ongkir: Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></p>
                        <?php endif; ?>
                        <h5 class="text-primary">Total: Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></h5>
                    </div>
                </div>

                <!-- Riwayat pembayaran (dari tabel payments) -->
                <?php if (!empty($order['payments'])): ?>
                    <hr>
                    <div class="mt-2">
                        <strong>Riwayat Pembayaran:</strong>
                        <ul class="list-unstyled small">
                            <?php foreach ($order['payments'] as $pay): ?>
                                <li class="mb-1">
                                    <i class="fas fa-credit-card"></i> <?= strtoupper(str_replace('_', ' ', $pay['payment_method'])) ?>
                                    - Rp <?= number_format($pay['amount'], 0, ',', '.') ?>
                                    - Status: 
                                    <span class="badge 
                                        <?= $pay['status'] == 'success' ? 'bg-success' : ($pay['status'] == 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                                        <?= ucfirst($pay['status']) ?>
                                    </span>
                                    <?php if ($pay['proof']): ?>
                                        - <a href="public/assets/uploads/proofs/<?= $pay['proof'] ?>" target="_blank">Lihat Bukti</a>
                                    <?php endif; ?>
                                    <?php if ($pay['transaction_id']): ?>
                                        - ID Transaksi: <?= htmlspecialchars($pay['transaction_id']) ?>
                                    <?php endif; ?>
                                    <br><small><?= date('d/m/Y H:i', strtotime($pay['payment_date'])) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Aksi untuk pesanan pending (tanpa Midtrans) -->
                <?php if ($order['status'] == 'pending'): ?>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <a href="index.php?url=order/uploadProofForm/<?= $order['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-upload"></i> Upload Bukti
                        </a>
                        <a href="index.php?url=order/cancel/<?= $order['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan pesanan?')">
                            <i class="fas fa-times"></i> Batalkan
                        </a>
                    </div>
                <?php elseif ($order['status'] == 'paid'): ?>
                    <div class="mt-3 text-success">
                        <i class="fas fa-check-circle"></i> Pembayaran telah dikonfirmasi. Silakan download produk di atas.
                    </div>
                <?php elseif ($order['status'] == 'cancelled'): ?>
                    <div class="mt-3 text-danger">
                        <i class="fas fa-times-circle"></i> Pesanan dibatalkan.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>