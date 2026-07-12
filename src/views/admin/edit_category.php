<?php
// src/views/admin/edit_category.php
$category = $category ?? null;
if (!$category) die("Kategori tidak ditemukan.");
include __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Kategori</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?url=admin/editCategory/<?= $category['id'] ?>">
                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php?url=admin/categories" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>