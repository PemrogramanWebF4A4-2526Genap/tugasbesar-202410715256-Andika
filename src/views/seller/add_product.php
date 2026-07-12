<?php
// src/views/seller/add_product.php
$categories = $categories ?? [];
$errors = $_SESSION['product_errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['product_errors'], $_SESSION['old_input']);
include __DIR__ . '/../templates/header.php';
?>

<h2><i class="fas fa-plus-circle"></i> Tambah Produk Baru</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="index.php?url=seller/addProduct" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($old['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($old['price'] ?? '') ?>" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>File Produk <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" required>
                        <small class="text-muted">Max 30MB (zip, pdf, exe, mp3, mp4, doc, xls, dll)</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label>Cover / Gambar Produk <span class="text-danger">*</span></label>
                <input type="file" name="cover" class="form-control" accept="image/*" required>
                <small class="text-muted">Max 5MB (jpg, jpeg, png, gif, webp)</small>
                <div id="cover-preview" class="mt-2"></div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Produk</button>
            <a href="index.php?url=seller/dashboard" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
    document.querySelector('input[name="cover"]')?.addEventListener('change', function(e) {
        const preview = document.getElementById('cover-preview');
        preview.innerHTML = '';
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = document.createElement('img');
                img.src = ev.target.result;
                img.style.maxWidth = '200px';
                img.style.maxHeight = '150px';
                img.classList.add('rounded', 'mt-2', 'border');
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>