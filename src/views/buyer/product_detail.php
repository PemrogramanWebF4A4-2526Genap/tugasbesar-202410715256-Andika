<?php
// src/views/buyer/product_detail.php
$product = $product ?? null;
$reviews = $reviews ?? [];
$avgRating = $avgRating ?? 0;
$isWishlisted = $isWishlisted ?? false;
if (!$product) die("Produk tidak ditemukan.");
include __DIR__ . '/../templates/header.php';
?>

<div class="product-detail">
    <div class="row">
        <div class="col-md-5">
            <img src="public/assets/uploads/products/<?= htmlspecialchars($product['cover_image'] ?? 'default.jpg') ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="col-md-7">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <div class="text-muted mb-3">
                <i class="fas fa-store"></i> Penjual: <?= htmlspecialchars($product['seller_name']) ?>
                &nbsp;|&nbsp; <i class="fas fa-tag"></i> <?= htmlspecialchars($product['category_name']) ?>
            </div>
            <div class="mb-2">
                <span class="text-warning">
                    <?php
                    $fullStars = floor($avgRating);
                    $halfStar = ($avgRating - $fullStars) >= 0.5;
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $fullStars) echo '⭐';
                        elseif ($halfStar) { echo '½⭐'; $halfStar = false; }
                        else echo '☆';
                    }
                    ?>
                </span>
                <span class="ms-2"><?= number_format($avgRating, 1) ?> / 5 (<?= count($reviews) ?> ulasan)</span>
            </div>
            <div class="price-large mb-3">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
            <div class="description my-4">
                <?= nl2br(htmlspecialchars($product['description'] ?? '')) ?>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn-add-cart add-to-cart" data-id="<?= $product['id'] ?>">
                        <i class="fas fa-cart-plus"></i> Keranjang
                    </button>
                    <a href="index.php?url=cart/addAndRedirect/<?= $product['id'] ?>" class="btn-buy-now btn btn-primary">
                        Beli Sekarang
                    </a>
                    <?php if ($isWishlisted): ?>
                        <a href="index.php?url=wishlist/remove/<?= $product['id'] ?>" class="btn btn-outline-danger">
                            <i class="fas fa-heart"></i> Hapus Wishlist
                        </a>
                    <?php else: ?>
                        <a href="index.php?url=wishlist/add/<?= $product['id'] ?>" class="btn btn-outline-primary">
                            <i class="far fa-heart"></i> Wishlist
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="index.php?url=auth/loginForm" class="btn btn-warning">Login untuk membeli</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Review Section -->
    <div class="mt-5">
        <h4>⭐ Ulasan Produk</h4>
        <?php if (empty($reviews)): ?>
            <div class="alert alert-info">Belum ada ulasan untuk produk ini.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($reviews as $rv): ?>
                    <div class="list-group-item mb-2 rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><?= htmlspecialchars($rv['user_name']) ?></strong>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($rv['created_at'])) ?></small>
                        </div>
                        <div class="text-warning small">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?= $i <= $rv['rating'] ? '⭐' : '☆' ?>
                            <?php endfor; ?>
                        </div>
                        <p class="mt-2 mb-1"><?= nl2br(htmlspecialchars($rv['comment'])) ?></p>
                        <?php if (!empty($rv['photo'])): ?>
                            <div class="mt-1">
                                <a href="public/assets/uploads/reviews/<?= $rv['photo'] ?>" target="_blank">
                                    <img src="public/assets/uploads/reviews/<?= $rv['photo'] ?>" width="80" class="rounded border">
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelector('.add-to-cart')?.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('index.php?url=cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const badge = document.querySelector('.badge-cart');
                if (badge) badge.innerText = data.totalItems;
            } else {
                alert(data.message);
            }
        });
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>