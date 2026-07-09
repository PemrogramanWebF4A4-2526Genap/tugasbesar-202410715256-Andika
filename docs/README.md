# 🛒 Digital Product Store – Toko Produk Digital

Aplikasi e-commerce untuk menjual produk digital (ebook, template, software, audio, video) yang dibangun dengan **PHP Native**, **MySQL**, **Bootstrap 5**, dan **Midtrans Payment Gateway** (opsional). Aplikasi ini mendukung tiga peran: **Admin**, **Seller**, dan **Buyer**.

![DigiStore Logo](public/assets/images/Logo.png)

---

## 📌 Fitur Utama

### 👤 Pembeli (Buyer)
- Registrasi & Login dengan validasi + **Remember Me**
- Lihat daftar produk (kategori, pencarian, filter harga, sorting)
- **Keranjang belanja** (tambah, update jumlah, hapus) dengan AJAX
- **Checkout** dengan alamat pengiriman & simulasi ongkos kirim
- **Pembayaran**: upload bukti manual **atau** melalui Midtrans (otomatis) – opsional
- **Download produk** setelah pembayaran sukses
- **Review & Rating** (bintang 1-5 + komentar + foto)
- **Wishlist** (daftar produk favorit)
- **Notifikasi in-app** dan **email** (registrasi, pesanan, konfirmasi pembayaran)

### 🧑‍💼 Penjual (Seller)
- Dashboard statistik (total produk, total penjualan, total pesanan, rata‑rata per pesanan)
- **CRUD produk** (upload file produk & cover, pilih kategori, status pending)
- Lihat **pesanan masuk** (detail pesanan + bukti bayar)
- **Konfirmasi pembayaran** (ubah status pending → paid)

### 👑 Admin
- Dashboard dengan **grafik penjualan 7 hari terakhir** (Chart.js)
- **Manajemen User** (pagination, pencarian, ubah role, hapus)
- **Manajemen Kategori** (tambah, edit, hapus)
- **Approve / reject produk** yang masih pending
- **Semua pesanan** (filter status, pagination, export CSV)

### 🔐 Keamanan
- Password hashing (bcrypt)
- SQL Injection prevention (prepared statements)
- XSS protection (`htmlspecialchars()`)
- Validasi file upload (tipe & ukuran)
- Remember token untuk auto-login

---

## 🧱 Teknologi

| Komponen        | Teknologi                                                        |
|----------------|------------------------------------------------------------------|
| Backend        | PHP Native (tanpa framework)                                     |
| Database       | MySQL / MariaDB                                                  |
| Frontend       | HTML5, CSS3, Bootstrap 5, JavaScript (AJAX)                      |
| Font & Ikon    | Google Fonts (Inter), Font Awesome 6                             |
| Payment Gateway| Midtrans (Snap API + Webhook) – opsional                         |
| Email          | PHPMailer (SMTP Gmail)                                           |
| Grafik         | Chart.js                                                         |
| Server         | XAMPP / Laragon / Docker                                         |

---

## 📂 Struktur Folder
UAS_INFO2425_202410715256_ANDIKA_ABDI_PUTRA_SEPTIANTO2/
├── index.php # Front controller & routing
├── .htaccess # URL rewriting
├── .env # Environment variables (Midtrans, SMTP)
├── create_admin.php # (opsional) buat akun admin
├── src/
│ ├── config/
│ │ ├── database.php # Koneksi PDO
│ │ └── payment.php # Konfigurasi Midtrans (jika digunakan)
│ ├── controllers/
│ │ ├── AuthController.php
│ │ ├── ProductController.php
│ │ ├── CartController.php
│ │ ├── OrderController.php
│ │ ├── CheckoutController.php
│ │ ├── PaymentController.php
│ │ ├── AdminController.php
│ │ ├── SellerController.php
│ │ ├── BuyerController.php
│ │ ├── ReviewController.php
│ │ ├── WishlistController.php
│ │ ├── StoreController.php
│ │ └── NotificationController.php
│ ├── models/
│ │ ├── User.php
│ │ ├── Category.php
│ │ ├── Product.php
│ │ ├── Order.php
│ │ ├── OrderItem.php
│ │ ├── Review.php
│ │ ├── Wishlist.php
│ │ ├── Payment.php
│ │ └── Notification.php
│ ├── views/
│ │ ├── templates/
│ │ │ ├── header.php
│ │ │ └── footer.php
│ │ ├── auth/
│ │ ├── buyer/
│ │ ├── seller/
│ │ ├── admin/
│ │ ├── store/
│ │ └── notifications/
│ └── helpers/
│ └── EmailHelper.php
├── public/
│ └── assets/
│ ├── css/
│ ├── js/
│ ├── images/
│ └── uploads/
│ ├── products/ # file produk & cover
│ ├── proofs/ # bukti pembayaran
│ └── reviews/ # foto review
├── database/
│ └── database.sql # skema lengkap + data awal
├── docs/
│ ├── README.md # file ini
│ ├── USER_MANUAL.md # panduan pengguna
│ └── DATABASE_SCHEMA.md # skema database
└── presentation/
└── PRESENTASI_UAS.pptx # slide presentasi

---

## 🚀 Cara Install & Menjalankan

### 1. Prasyarat
- PHP ≥ 7.4
- MySQL / MariaDB
- Composer (untuk PHPMailer & Midtrans SDK)
- XAMPP / Laragon / server lokal lain

### 2. Clone / Download Proyek
Letakkan folder proyek di dalam `htdocs` (XAMPP) atau `www` (Laragon).

### 3. Buat Database
- Buka phpMyAdmin, buat database baru (misal `digital_product_store`).
- Import file `database/database.sql` (tersedia di folder `database/`).

### 4. Konfigurasi Database
- Buka `src/config/database.php`
- Sesuaikan `$host`, `$dbname`, `$username`, `$password`.

### 5. Install Dependensi (via Composer)
Buka terminal di root proyek, jalankan:
```bash
composer require phpmailer/phpmailer
composer require midtrans/midtrans-php

### 6. Setup Environment (.env)
Buat file .env di root, isi dengan:
# Midtrans (opsional)
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false

# Email SMTP (opsional)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=emailkamu@gmail.com
SMTP_PASS=your_app_password
SMTP_FROM_EMAIL=emailkamu@gmail.com
SMTP_FROM_NAME=Digital Product Store

### 7. Set Permission Folder Upload
Pastikan folder public/assets/uploads/ dan subfoldernya (products, proofs, reviews) memiliki izin tulis (777 atau 755).

### 8. Akses Aplikasi
Buka browser: http://localhost/UAS_INFO2425_.../

Akun default:

Admin: admin@example.com / admin123

Seller / Buyer: daftar sendiri


📧 Konfigurasi Email
Aktifkan 2FA pada akun Gmail, lalu buat App Password (16 karakter).

Masukkan App Password ke SMTP_PASS di .env.

Jika tidak ingin email, biarkan kosong – aplikasi tetap berjalan (email akan gagal tapi tidak error).

🛠️ Pengembangan Lebih Lanjut
Integrasi login sosial (Google/Facebook)

Live chat antara buyer & seller

Sistem rekomendasi produk

Aplikasi mobile (API)

Notifikasi WhatsApp (WhatsApp Business API)

🤝 Kontribusi
Silakan buat issue atau pull request jika ingin memperbaiki/menambah fitur.

📝 Lisensi
Proyek ini dibuat untuk keperluan Ujian Akhir Semester (UAS) dan dapat dikembangkan lebih lanjut.

Dibuat dengan ❤️ untuk Digital Product Store

This response is AI-generated, for reference only.