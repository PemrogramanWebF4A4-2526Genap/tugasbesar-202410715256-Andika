<?php
// src/views/auth/login.php
$errors = $_SESSION['login_errors'] ?? [];
$old_email = $_SESSION['old_email'] ?? '';
unset($_SESSION['login_errors'], $_SESSION['old_email']);
include __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <img src="public/assets/images/Logo.png" alt="DigiStore" height="60" class="mb-2">
                    <h2 class="fw-bold">Login</h2>
                    <p class="text-muted">Masuk ke akun DigiStore Anda</p>
                </div>

                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="alert alert-success">Registrasi berhasil! Silakan login.</div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?url=auth/login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($old_email) ?>" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <p class="mt-3 text-center">
                    Belum punya akun? <a href="index.php?url=auth/registerForm">Daftar</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>