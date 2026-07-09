<?php
// src/views/store/index.php
// Data yang dikirim dari StoreController: $seller, $products, $rating
$seller = $seller ?? null;
$products = $products ?? [];
$rating = $rating ?? ['avg_rating' => 0, 'total_reviews' => 0];

if (!$seller) {
    die("Toko tidak ditemukan.");
}

include __DIR__ . '/../templates/header.php';
?>

<div class="store-header mb-4 p-4 bg-white rounded shadow-sm">
    <div class="row align-items-center">
        <div class="col-md-2 text-center">
            <i class="fas fa-store fa-5x text-primary"></i>
        </div>
        <div class="col-md-10">
            <h2><?= htmlspecialchars($seller['name']) ?></h2>
            <p class="text-muted">
                Bergabung sejak <?= date('d/m/Y', strtotime($seller['created_at'])) ?>
            </p>
            <p><?= count($products) ?> produk digital</p>

            <?php if ($rating && $rating['total_reviews'] > 0): ?>
                <div>
                    <span class="text-warning">
                        <?php
                        $avg = (float) $rating['avg_rating'];
                        $fullStars = floor($avg);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $fullStars) echo '⭐';
                            else echo '☆';
                        }
                        ?>
                    </span>
                    <span class="ms-2">
                        <?= number_format($avg, 1) ?> / 5
                        (<?= $rating['total_reviews'] ?> ulasan)
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<h3>Semua Produk dari Toko Ini</h3>

<?php if (empty($products)): ?>
    <div class="alert alert-info">Belum ada produk dari toko ini.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($products as $p): ?>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="product-card">
                    <img src="public/assets/uploads/products/<?= htmlspecialchars($p['cover_image'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    <div class="card-body">
                        <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        <div class="seller-name"><i class="fas fa-store"></i> <?= htmlspecialchars($seller['name']) ?></div>
                        <a href="index.php?url=product/detail/<?= $p['id'] ?>" class="btn btn-buy mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>