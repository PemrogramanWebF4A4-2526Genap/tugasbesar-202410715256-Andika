<?php
// src/views/auth/register.php
$errors = $_SESSION['register_errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['old_input']);
include __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <img src="public/assets/images/Logo.png" alt="DigiStore" height="60" class="mb-2">
                    <h2 class="fw-bold">Daftar Akun</h2>
                    <p class="text-muted">Bergabung dengan DigiStore</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?url=auth/register" id="registerForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required minlength="3" pattern=".{3,}" title="Minimal 3 karakter">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (min. 6 karakter)</label>
                        <input type="password" name="password" id="password" class="form-control" required minlength="6" pattern=".{6,}">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        <small id="passwordMatchMsg" class="text-danger" style="display:none;">Password tidak cocok</small>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Daftar sebagai</label>
                        <select name="role" id="role" class="form-select">
                            <option value="buyer" <?= ($old['role'] ?? '') == 'buyer' ? 'selected' : '' ?>>Pembeli</option>
                            <option value="seller" <?= ($old['role'] ?? '') == 'seller' ? 'selected' : '' ?>>Penjual</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Daftar</button>
                </form>

                <p class="mt-3 text-center">
                    Sudah punya akun? <a href="index.php?url=auth/loginForm">Login</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        const msg = document.getElementById('passwordMatchMsg');
        const form = document.getElementById('registerForm');

        function validatePasswordMatch() {
            if (password.value !== confirm.value) {
                msg.style.display = 'block';
                return false;
            } else {
                msg.style.display = 'none';
                return true;
            }
        }

        confirm.addEventListener('keyup', validatePasswordMatch);
        form.addEventListener('submit', function(e) {
            if (!validatePasswordMatch()) {
                e.preventDefault();
                alert('Password dan konfirmasi password harus sama.');
            }
        });
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>