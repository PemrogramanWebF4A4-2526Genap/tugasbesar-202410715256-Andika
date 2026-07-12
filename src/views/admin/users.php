<?php
// src/views/admin/users.php
$users = $users ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$search = $_GET['search'] ?? '';
include __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2><i class="fas fa-users"></i> Manajemen User</h2>
    <form method="GET" action="index.php" class="d-flex gap-2">
        <input type="hidden" name="url" value="admin/users">
        <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="5" class="text-center py-3">Tidak ada user.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="POST" action="index.php?url=admin/editUser/<?= $user['id'] ?>" class="d-inline">
                                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="buyer" <?= $user['role'] == 'buyer' ? 'selected' : '' ?>>Buyer</option>
                                        <option value="seller" <?= $user['role'] == 'seller' ? 'selected' : '' ?>>Seller</option>
                                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href="index.php?url=admin/deleteUser/<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus user ini?')">
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

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?url=admin/users&page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>