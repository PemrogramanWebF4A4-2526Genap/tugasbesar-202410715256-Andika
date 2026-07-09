<?php
// src/controllers/AuthController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';

class AuthController {
    private $userModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    // ============================================================
    // TAMPILAN FORM
    // ============================================================

    public function loginForm() {
        include __DIR__ . '/../views/auth/login.php';
    }

    public function registerForm() {
        include __DIR__ . '/../views/auth/register.php';
    }

    // ============================================================
    // REGISTRASI
    // ============================================================

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=auth/registerForm");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'buyer';
        $errors = [];

        // Validasi
        if (empty($name)) {
            $errors[] = "Nama harus diisi.";
        } elseif (strlen($name) < 3) {
            $errors[] = "Nama minimal 3 karakter.";
        }

        if (empty($email)) {
            $errors[] = "Email harus diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid.";
        } elseif ($this->userModel->emailExists($email)) {
            $errors[] = "Email sudah terdaftar.";
        }

        if (empty($password)) {
            $errors[] = "Password harus diisi.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password minimal 6 karakter.";
        } elseif ($password !== $confirm) {
            $errors[] = "Konfirmasi password tidak cocok.";
        }

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email, 'role' => $role];
            header("Location: index.php?url=auth/registerForm");
            exit;
        }

        // Simpan user
        if ($this->userModel->create($name, $email, $password, $role)) {
            $userId = $this->pdo->lastInsertId();

            // Notifikasi in-app
            $notif = new Notification($this->pdo);
            $notif->create($userId, 'welcome', 'Selamat Datang', "Halo $name, selamat bergabung di DigiStore!", 'index.php?url=home');

            // Email notifikasi
            $emailHelper = new EmailHelper();
            $emailHelper->send($email, $name, 'Selamat Datang', EmailHelper::getRegisterTemplate($name));

            header("Location: index.php?url=auth/loginForm&success=1");
            exit;
        }

        $_SESSION['register_errors'] = ['Gagal mendaftar. Silakan coba lagi.'];
        header("Location: index.php?url=auth/registerForm");
        exit;
    }

    // ============================================================
    // LOGIN
    // ============================================================

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=auth/loginForm");
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        $errors = [];

        if (empty($email)) {
            $errors[] = "Email harus diisi.";
        }
        if (empty($password)) {
            $errors[] = "Password harus diisi.";
        }

        if (empty($errors)) {
            $user = $this->userModel->findByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $this->userModel->updateRememberToken($user['id'], $token);
                    setcookie('remember_token', $token, time() + 86400 * 30, "/");
                }

                // Redirect berdasarkan role
                if ($user['role'] === 'admin') {
                    header("Location: index.php?url=admin/dashboard");
                } elseif ($user['role'] === 'seller') {
                    header("Location: index.php?url=seller/dashboard");
                } else {
                    header("Location: index.php?url=home");
                }
                exit;
            }
            $errors[] = "Email atau password salah.";
        }

        $_SESSION['login_errors'] = $errors;
        $_SESSION['old_email'] = $email;
        header("Location: index.php?url=auth/loginForm");
        exit;
    }

    // ============================================================
    // LOGOUT
    // ============================================================

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->userModel->clearRememberToken($_SESSION['user_id']);
        }
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, "/");
        }
        session_destroy();
        header("Location: index.php?url=home");
        exit;
    }

    // ============================================================
    // AUTO LOGIN (REMEMBER ME)
    // ============================================================

    public static function autoLogin($pdo) {
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $userModel = new User($pdo);
            $user = $userModel->findByRememberToken($token);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];
            }
        }
    }
}
?>