<?php
// src/views/buyer/wishlist.php
$products = $products ?? [];
include __DIR__ . '/../templates/header.php';
?>

<h2><i class="fas fa-heart text-danger"></i> Wishlist Saya</h2>

<?php if (empty($products)): ?>
    <div class="alert alert-info">Wishlist kosong. <a href="index.php?url=home">Belanja sekarang</a></div>
<?php else: ?>
    <div class="row">
        <?php foreach ($products as $p): ?>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="product-card">
                    <img src="public/assets/uploads/products/<?= htmlspecialchars($p['cover_image'] ?? 'default.jpg') ?>" alt="">
                    <div class="card-body">
                        <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        <div class="seller-name"><?= htmlspecialchars($p['seller_name']) ?></div>
                        <div class="mt-2">
                            <a href="index.php?url=product/detail/<?= $p['id'] ?>" class="btn btn-primary btn-sm w-100">Lihat</a>
                            <a href="index.php?url=wishlist/remove/<?= $p['id'] ?>" class="btn btn-outline-danger btn-sm w-100 mt-1" onclick="return confirm('Hapus dari wishlist?')">Hapus</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>