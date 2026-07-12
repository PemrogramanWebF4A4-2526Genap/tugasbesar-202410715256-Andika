<?php
// src/views/buyer/payment_success.php
$order = $order ?? null;
if (!$order) {
    die("Pesanan tidak ditemukan.");
}
include __DIR__ . '/../templates/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="fw-bold text-success">Pembayaran Berhasil!</h3>
                    <p class="lead">Pesanan #<?= htmlspecialchars($order['id']) ?> telah berhasil dibayar.</p>
                    <p>Anda sekarang dapat mengunduh produk digital Anda.</p>
                    
                    <div class="mt-4">
                        <a href="index.php?url=buyer/myOrders" class="btn btn-success btn-lg">
                            <i class="fas fa-download me-2"></i> Lihat & Download
                        </a>
                        <a href="index.php?url=home" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="fas fa-home me-2"></i> Kembali ke Beranda
                        </a>
                    </div>

                    <!-- Detail singkat pesanan (opsional) -->
                    <div class="mt-4 text-start border-top pt-3">
                        <p class="mb-1"><strong>Total:</strong> Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></p>
                        <p class="mb-0"><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>