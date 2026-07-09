<?php
// src/views/buyer/checkout.php
$items = $items ?? [];
$subtotal = $subtotal ?? 0;
$shippingCost = $shippingCost ?? 0;
$total = $total ?? 0;
$error = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);
include __DIR__ . '/../templates/header.php';
?>

<h2><i class="fas fa-clipboard-check"></i> Checkout</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-7">
        <form method="POST" action="index.php?url=checkout/process" id="checkoutForm">
            <div class="card mb-3">
                <div class="card-header">Alamat Pengiriman</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Kota</label>
                            <select name="city" id="city" class="form-control" required>
                                <option value="">Pilih Kota</option>
                                <option value="Jakarta">Jakarta</option>
                                <option value="Bandung">Bandung</option>
                                <option value="Surabaya">Surabaya</option>
                                <option value="Medan">Medan</option>
                                <option value="Yogyakarta">Yogyakarta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Kode Pos</label>
                            <input type="text" name="postal_code" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Metode Pembayaran</div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer" id="bank" checked>
                        <label class="form-check-label" for="bank">Transfer Bank (BCA/Mandiri/BNI)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="qris" id="qris">
                        <label class="form-check-label" for="qris">QRIS</label>
                    </div>
                </div>
            </div>
            <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
            <button type="submit" class="btn btn-success">Buat Pesanan</button>
        </form>
    </div>
    <div class="col-md-5">
        <div class="cart-summary">
            <h5 class="fw-bold">Ringkasan Pesanan</h5>
            <table class="table table-sm">
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></td>
                    <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr><td><strong>Subtotal</strong></td><td class="text-end"><strong id="subtotal"><?= number_format($subtotal, 0, ',', '.') ?></strong></td></tr>
                <tr><td>Ongkos Kirim</td><td class="text-end" id="shipping_display">Rp 0</td></tr>
                <tr class="table-active"><td><strong>Total</strong></td><td class="text-end"><strong id="grand_total"><?= number_format($total, 0, ',', '.') ?></strong></td></tr>
            </table>
        </div>
    </div>
</div>

<script>
    const citySelect = document.getElementById('city');
    const shippingCostInput = document.getElementById('shipping_cost');
    const shippingDisplay = document.getElementById('shipping_display');
    const subtotal = <?= $subtotal ?>;
    const grandTotalSpan = document.getElementById('grand_total');

    function updateShipping() {
        let cost = 0;
        const city = citySelect.value;
        switch(city) {
            case 'Jakarta': cost = 20000; break;
            case 'Bandung': cost = 25000; break;
            case 'Surabaya': cost = 35000; break;
            case 'Medan': cost = 50000; break;
            case 'Yogyakarta': cost = 20000; break;
            default: cost = 0;
        }
        shippingCostInput.value = cost;
        shippingDisplay.innerText = 'Rp ' + cost.toLocaleString('id-ID');
        const total = subtotal + cost;
        grandTotalSpan.innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
    citySelect.addEventListener('change', updateShipping);
    updateShipping();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>