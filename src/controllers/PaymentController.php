<?php
// src/controllers/PaymentController.php
// 🔒 Payment Gateway Dinonaktifkan – Menggunakan Pembayaran Manual (Upload Bukti)

class PaymentController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Tidak perlu load model Order/Payment karena Midtrans dimatikan
    }

    // ============================================================
    // 1. PAY – Redirect ke Upload Bukti
    // ============================================================
    public function pay($orderId) {
        // Cek login
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/loginForm");
            exit;
        }

        // Redirect langsung ke halaman upload bukti pembayaran
        header("Location: index.php?url=order/uploadProofForm/" . (int)$orderId);
        exit;
    }

    // ============================================================
    // 2. SUCCESS – Redirect ke Pesanan Saya
    // ============================================================
    public function success() {
        // Cek login
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/loginForm");
            exit;
        }

        // Arahkan ke halaman pesanan buyer
        header("Location: index.php?url=buyer/myOrders");
        exit;
    }

    // ============================================================
    // 3. WEBHOOK – Tidak digunakan (Midtrans dimatikan)
    // ============================================================
    public function webhook() {
        // Beri respons OK agar Midtrans tidak mengirim ulang notifikasi
        http_response_code(200);
        echo "Midtrans disabled – payment is manual.";
        exit;
    }
}
?>