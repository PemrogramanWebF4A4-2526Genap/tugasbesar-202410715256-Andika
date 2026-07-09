<?php
// src/views/buyer/cart.php
$cartItems = $cartItems ?? [];
$total = $total ?? 0;
include __DIR__ . '/../templates/header.php';
?>

<h3 class="fw-bold mb-4"><i class="fas fa-shopping-cart me-2"></i> Keranjang Belanja</h3>

<?php if (empty($cartItems)): ?>
    <div class="alert alert-info text-center">Keranjang kosong. <a href="index.php?url=home">Belanja sekarang</a></div>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <div class="cart-table table-responsive">
            <table class="table align-middle" id="cart-table">
                <thead>
                    <tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                    <tr data-product-id="<?= $item['id'] ?>">
                        <td>
                            <img src="public/assets/uploads/products/<?= htmlspecialchars($item['cover'] ?? 'default.jpg') ?>" width="60" class="rounded me-2">
                            <?= htmlspecialchars($item['name']) ?>
                        </td>
                        <td class="price">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td><input type="number" class="form-control qty-input" value="<?= $item['quantity'] ?>" min="1" style="width:80px"></td>
                        <td class="subtotal">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                        <td><button class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-trash"></i></button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="cart-summary">
            <h5 class="fw-bold">Ringkasan Belanja</h5>
            <hr>
            <div class="d-flex justify-content-between mb-2">
                <span>Total Barang</span>
                <span id="total-items"><?= array_sum(array_column($cartItems, 'quantity')) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Total Harga</span>
                <strong id="cart-total">Rp <?= number_format($total, 0, ',', '.') ?></strong>
            </div>
            <a href="index.php?url=checkout/index" class="btn btn-success w-100 py-2 fw-bold"><i class="fas fa-arrow-right"></i> Checkout</a>
            <a href="index.php?url=home" class="btn btn-outline-secondary w-100 mt-2">Lanjut Belanja</a>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="public/assets/js/cart.js"></script>
<?php include __DIR__ . '/../templates/footer.php'; ?>