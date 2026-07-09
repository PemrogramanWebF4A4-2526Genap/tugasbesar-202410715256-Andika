<?php
// src/views/buyer/products.php
$products = $products ?? [];
$categories = $categories ?? [];
$bestSellers = $bestSellers ?? [];
$keyword = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$minPrice = $minPrice ?? 0;
$maxPrice = $maxPrice ?? 0;
$page = $page ?? 1;
$total = $total ?? 0;
include __DIR__ . '/../templates/header.php';
?>

<div class="row">
    <!-- Sidebar Kategori + Filter -->
    <div class="col-md-3">
        <div class="category-sidebar mb-4">
            <h6 class="px-3 pt-2 fw-bold">Kategori</h6>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="index.php?url=home">Semua</a></li>
                <?php foreach ($categories as $cat): ?>
                    <li class="list-group-item"><a href="index.php?url=product/category/<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Filter Harga -->
        <div class="card mb-4">
            <div class="card-header">Filter Harga</div>
            <div class="card-body">
                <form method="GET" action="index.php">
                    <input type="hidden" name="url" value="product/search">
                    <input type="hidden" name="q" value="<?= htmlspecialchars($keyword) ?>">
                    <div class="mb-2">
                        <label>Minimal</label>
                        <input type="number" name="min_price" class="form-control" value="<?= $minPrice ?>">
                    </div>
                    <div class="mb-2">
                        <label>Maksimal</label>
                        <input type="number" name="max_price" class="form-control" value="<?= $maxPrice ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                </form>
            </div>
        </div>

        <!-- Best Seller Widget -->
        <div class="card">
            <div class="card-header bg-warning text-dark">🔥 Produk Terlaris</div>
            <div class="card-body p-2">
                <?php foreach ($bestSellers as $bs): ?>
                    <div class="d-flex align-items-center mb-2 border-bottom pb-2">
                        <img src="public/assets/uploads/products/<?= htmlspecialchars($bs['cover_image'] ?? 'default.jpg') ?>" width="50" height="50" class="rounded me-2" style="object-fit:cover">
                        <a href="index.php?url=product/detail/<?= $bs['id'] ?>" class="small"><?= htmlspecialchars($bs['name']) ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Daftar Produk -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h4 class="fw-bold">Produk Digital</h4>
            <div class="d-flex gap-2">
                <span class="text-muted"><?= $total ?> produk</span>
                <select id="sortSelect" class="form-select form-select-sm w-auto">
                    <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Terbaru</option>
                    <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Harga Terendah</option>
                    <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                    <option value="best_seller" <?= $sort == 'best_seller' ? 'selected' : '' ?>>Best Seller</option>
                </select>
            </div>
        </div>

        <div class="row">
            <?php foreach ($products as $p): ?>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="product-card">
                        <img src="public/assets/uploads/products/<?= htmlspecialchars($p['cover_image'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        <div class="card-body">
                            <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                            <div class="seller-name"><i class="fas fa-store"></i> <?= htmlspecialchars($p['seller_name']) ?></div>
                            <a href="index.php?url=product/detail/<?= $p['id'] ?>" class="btn btn-buy mt-2">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5>Belum ada produk</h5>
                    <p class="text-muted">Coba kata kunci lain atau jelajahi kategori.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total > 12): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= ceil($total / 12); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('sortSelect')?.addEventListener('change', function() {
        let url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>