<?php
// src/views/buyer/review_form.php
$product = $product ?? null;
$orderId = $orderId ?? 0;
if (!$product) die("Produk tidak ditemukan.");
include __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-star"></i> Beri Review</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="public/assets/uploads/products/<?= htmlspecialchars($product['cover_image'] ?? 'default.jpg') ?>" class="img-fluid rounded" style="max-height:150px;">
                    <h5 class="mt-2"><?= htmlspecialchars($product['name']) ?></h5>
                </div>

                <form method="POST" action="index.php?url=review/submit" enctype="multipart/form-data">
                    <input type="hidden" name="order_id" value="<?= (int)$orderId ?>">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

                    <div class="mb-3">
                        <label class="fw-bold">Rating <span class="text-danger">*</span></label>
                        <select name="rating" class="form-select" required>
                            <option value="">Pilih rating</option>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>"><?= str_repeat('⭐', $i) ?> (<?= $i ?> bintang)</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Komentar <span class="text-danger">*</span></label>
                        <textarea name="comment" class="form-control" rows="5" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold">Foto (Opsional)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <div id="photo-preview" class="mt-2"></div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?url=buyer/myOrders" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Kirim Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('input[name="photo"]')?.addEventListener('change', function(e) {
        const preview = document.getElementById('photo-preview');
        preview.innerHTML = '';
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = document.createElement('img');
                img.src = ev.target.result;
                img.style.maxWidth = '200px';
                img.classList.add('rounded', 'mt-2', 'border');
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>