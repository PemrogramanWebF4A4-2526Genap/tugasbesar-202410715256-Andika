<?php
// src/views/seller/edit_product.php
$product = $product ?? null;
$categories = $categories ?? [];
if (!$product) die("Produk tidak ditemukan.");
include __DIR__ . '/../templates/header.php';
?>

<h2><i class="fas fa-edit"></i> Edit Produk</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="index.php?url=seller/editProduct/<?= $product['id'] ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label>Harga (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" step="0.01" min="0" required>
            </div>

            <div class="mb-3">
                <label>Cover Saat Ini</label><br>
                <img src="public/assets/uploads/products/<?= htmlspecialchars($product['cover_image'] ?? 'default.jpg') ?>" width="100" class="rounded border">
                <small class="text-muted d-block">Untuk mengganti cover, upload ulang di form tambah produk (fitur ini tidak tersedia di edit).</small>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Produk</button>
            <a href="index.php?url=seller/dashboard" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>