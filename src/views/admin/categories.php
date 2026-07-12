<?php
// src/views/admin/categories.php
$categories = $categories ?? [];
include __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags"></i> Manajemen Kategori</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fas fa-plus"></i> Tambah Kategori
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>ID</th><th>Nama Kategori</th><th>Jumlah Produk</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="4" class="text-center py-3">Belum ada kategori.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= $cat['product_count'] ?? 0 ?></td>
                            <td>
                                <a href="index.php?url=admin/editCategoryForm/<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="index.php?url=admin/deleteCategory/<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="index.php?url=admin/addCategory">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>