<?php
// src/views/buyer/checkout_success.php
$order = $order ?? null;
$items = $items ?? [];
if (!$order) die("Pesanan tidak ditemukan.");
include __DIR__ . '/../templates/header.php';
?>

<div class="alert alert-success text-center">
    <h4><i class="fas fa-check-circle"></i> Pesanan Berhasil Dibuat!</h4>
    <p>Terima kasih telah berbelanja di DigiStore.</p>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Detail Pesanan #<?= htmlspecialchars($order['id']) ?></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                <p><strong>Status:</strong> <span class="badge bg-warning"><?= ucfirst($order['status']) ?></span></p>
            </div>
            <div class="col-md-6">
                <p><strong>Alamat:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>, <?= $order['city'] ?> - <?= $order['postal_code'] ?></p>
                <p><strong>Metode:</strong> <?= strtoupper(str_replace('_', ' ', $order['payment_method'])) ?></p>
            </div>
        </div>

        <h6 class="mt-3">Produk Dipesan:</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-active"><th colspan="3" class="text-end">Total</th><th>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></th></tr>
                </tfoot>
            </table>
        </div>

        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> <strong>Instruksi Pembayaran:</strong>
            <ul class="mb-0 mt-2">
                <li>Lakukan transfer ke rekening: <strong>BCA 1234567890 a/n DigiStore</strong></li>
                <li>Total yang harus dibayar: <strong>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong></li>
                <li>Upload bukti transfer melalui tombol di bawah.</li>
                <li>Setelah bukti diupload, admin akan mengkonfirmasi pembayaran Anda.</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="index.php?url=order/uploadProofForm/<?= $order['id'] ?>" class="btn btn-warning btn-lg">
                <i class="fas fa-upload"></i> Upload Bukti Pembayaran
            </a>
            <a href="index.php?url=buyer/myOrders" class="btn btn-secondary btn-lg ms-2">
                <i class="fas fa-list"></i> Lihat Pesanan Saya
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>