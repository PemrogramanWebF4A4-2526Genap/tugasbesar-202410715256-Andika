# 📘 USER MANUAL – Digital Product Store

Selamat datang di **Digital Product Store**, aplikasi toko produk digital (ebook, template, software, audio, video). Panduan ini akan membantu Anda menggunakan semua fitur sebagai **Pembeli**, **Penjual**, maupun **Admin**.

---

## 📋 Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Panduan untuk Pembeli (Buyer)](#2-panduan-untuk-pembeli-buyer)
   - Registrasi & Login
   - Melihat Produk
   - Keranjang Belanja
   - Checkout
   - Pembayaran (Midtrans & Manual)
   - Download Produk
   - Review & Rating
   - Wishlist
   - Notifikasi & Email
3. [Panduan untuk Penjual (Seller)](#3-panduan-untuk-penjual-seller)
   - Registrasi sebagai Penjual
   - Menambah Produk Baru
   - Mengelola Produk
   - Melihat Pesanan Masuk
   - Konfirmasi Pembayaran
   - Dashboard Statistik
4. [Panduan untuk Admin](#4-panduan-untuk-admin)
   - Login sebagai Admin
   - Dashboard Admin
   - Manajemen User
   - Manajemen Kategori
   - Menyetujui Produk (Pending Products)
   - Melihat Semua Pesanan
5. [Notifikasi & Email](#5-notifikasi--email)
6. [Troubleshooting](#6-troubleshooting)

---

## 1. Pendahuluan

Aplikasi ini mendukung tiga jenis pengguna:
- **Pembeli (Buyer)** – membeli produk digital, melakukan pembayaran, memberi review.
- **Penjual (Seller)** – mengelola produk, melihat pesanan masuk, mengkonfirmasi pembayaran.
- **Admin** – mengelola pengguna, kategori, menyetujui produk, melihat semua transaksi.

---

## 2. Panduan untuk Pembeli (Buyer)

### 2.1 Registrasi dan Login
1. Buka halaman utama aplikasi.
2. Klik **Daftar** (Register) di pojok kanan atas.
3. Isi formulir: Nama, Email, Password, Konfirmasi Password, pilih role **Pembeli**.
4. Klik **Daftar**. Anda akan diarahkan ke halaman login.
5. Masukkan email dan password, centang **Ingat saya** (opsional) agar login otomatis di lain waktu.
6. Klik **Login**.

### 2.2 Melihat Produk
- Halaman utama menampilkan grid produk digital dengan gambar, nama, harga, dan nama penjual.
- Gunakan **sidebar kategori** di kiri untuk menyaring produk berdasarkan kategori (Ebook, Template, Software, dll).
- Gunakan **kotak pencarian** di navbar untuk mencari produk berdasarkan nama.
- Klik **Lihat Detail** pada produk untuk melihat informasi lengkap (deskripsi, rating, ulasan pembeli lain).

### 2.3 Keranjang Belanja
- Pada halaman detail produk, klik **Tambah ke Keranjang**.
- Pop-up notifikasi akan muncul dan jumlah keranjang di navbar akan bertambah.
- Klik ikon **Keranjang** di navbar untuk melihat daftar produk yang sudah ditambahkan.
- Di halaman keranjang, Anda dapat:
  - Mengubah jumlah produk dengan mengedit angka pada kolom **Jumlah**.
  - Menghapus produk dengan klik tombol **Hapus**.
- Klik **Lanjut Belanja** untuk kembali berbelanja, atau **Checkout** untuk melanjutkan ke pembayaran.

### 2.4 Checkout
1. Pada halaman keranjang, klik **Checkout**.
2. Isi **Alamat Pengiriman** (lengkap), **Kota**, dan **Kode Pos**.
3. Pilih **Metode Pembayaran**:
   - **Transfer Bank (BCA/Mandiri/BNI)**
   - **QRIS**
4. Klik **Buat Pesanan**.
5. Anda akan diarahkan ke halaman **Ringkasan Pesanan**.

### 2.5 Pembayaran

#### A. Pembayaran via Midtrans (Otomatis) – Jika Diaktifkan
- Pada halaman sukses checkout, klik **Bayar Sekarang (Midtrans)**.
- Anda akan diarahkan ke halaman Midtrans Snap. Pilih metode pembayaran (Kartu Kredit, Bank Transfer, QRIS, dll).
- Ikuti instruksi pembayaran. Jika menggunakan mode **Sandbox** (uji coba), gunakan kartu kredit dummy: `4811 1111 1111 1114`, CVV: `123`, masa berlaku: `12/25`.
- Setelah pembayaran berhasil, status pesanan otomatis berubah menjadi **Lunas (Paid)** dan Anda akan menerima notifikasi email & in-app.

#### B. Pembayaran Manual (Upload Bukti)
- Jika memilih metode Transfer Bank atau QRIS pada checkout, atau jika Midtrans tidak aktif, Anda harus mengunggah bukti pembayaran secara manual.
- Buka menu **Pesanan Saya** (setelah login).
- Pada pesanan dengan status **Pending**, klik **Upload Bukti Pembayaran**.
- Pilih file bukti (JPG, PNG, PDF), lalu klik **Upload**.
- Tunggu penjual/admin mengkonfirmasi pembayaran. Setelah dikonfirmasi, status menjadi **Paid** dan Anda dapat mendownload produk.

### 2.6 Download Produk
- Buka menu **Pesanan Saya**.
- Pada pesanan dengan status **Paid**, setiap produk memiliki tombol **Download**. Klik untuk mengunduh file digital.
- File akan tersimpan di perangkat Anda.

### 2.7 Memberi Review & Rating
- Setelah pesanan berstatus **Paid**, Anda dapat memberi review.
- Pada **Pesanan Saya**, di setiap produk yang sudah dibayar, klik tombol **Beri Review**.
- Pilih rating bintang (1–5), tulis komentar, dan unggah foto (opsional).
- Klik **Kirim Review**. Review akan tampil di halaman detail produk.

### 2.8 Wishlist (Daftar Keinginan)
- Pada halaman detail produk, klik tombol **Wishlist** (ikon hati) untuk menyimpan produk favorit.
- Klik menu **Wishlist** di dropdown profil untuk melihat daftar.
- Anda dapat menghapus produk dari wishlist atau langsung membelinya.

### 2.9 Notifikasi & Email
- Setiap ada pembaruan pesanan (pesanan baru, pembayaran dikonfirmasi), Anda akan menerima **notifikasi in-app** (muncul di navbar, ikon lonceng) dan **email** ke alamat yang terdaftar.

---

## 3. Panduan untuk Penjual (Seller)

### 3.1 Registrasi sebagai Penjual
- Saat daftar, pilih **Penjual** pada kolom "Daftar sebagai".
- Login dengan akun tersebut.

### 3.2 Menambah Produk Baru
- Pada dashboard penjual, klik **Tambah Produk Baru**.
- Isi formulir: Nama Produk, Kategori, Deskripsi, Harga.
- Upload **File Produk** (zip, pdf, exe, mp3, dll) dan **Cover/Gambar**.
- Klik **Simpan**. Produk akan berstatus **Pending** dan harus disetujui oleh admin terlebih dahulu.

### 3.3 Mengelola Produk
- Di dashboard, Anda dapat melihat daftar produk dengan status (Active/Pending).
- Klik **Edit** untuk mengubah data produk (kategori, nama, deskripsi, harga). File tidak dapat diubah melalui form edit (harus hapus dan tambah baru).
- Klik **Hapus** untuk menghapus produk.

### 3.4 Melihat Pesanan Masuk & Konfirmasi Pembayaran
- Klik menu **Pesanan Masuk**.
- Anda akan melihat daftar pesanan yang berisi produk Anda.
- Jika pembeli mengunggah bukti pembayaran, status pesanan tetap **Pending** dan muncul tautan **Lihat Bukti**.
- Setelah memeriksa bukti, klik tombol **Konfirmasi** untuk mengubah status menjadi **Paid**.
- Pembeli akan menerima notifikasi dan dapat langsung mendownload produk.

### 3.5 Dashboard Statistik
- Di halaman utama dashboard penjual, Anda dapat melihat:
  - Total produk Anda
  - Total penjualan (omzet)
  - Total pesanan selesai
  - Rata-rata per pesanan
  - Pesanan terbaru

---

## 4. Panduan untuk Admin

### 4.1 Login sebagai Admin
- Gunakan akun default: `admin@example.com` / `admin123` (atau email yang sudah Anda buat).
- Setelah login, menu **Admin Panel** akan muncul di navbar.

### 4.2 Dashboard Admin
- Menampilkan kartu statistik (total user, kategori, produk, pesanan).
- Grafik penjualan 7 hari terakhir (Chart.js).

### 4.3 Manajemen Pengguna (User)
- Buka **Admin Panel → Kelola User**.
- Anda dapat mencari user berdasarkan nama/email.
- Mengubah role (Buyer/Seller/Admin) dengan memilih dari dropdown.
- Menghapus user (tidak disarankan untuk admin utama).
- Pagination untuk navigasi halaman.

### 4.4 Manajemen Kategori
- Buka **Admin Panel → Kelola Kategori**.
- Tambah kategori baru melalui modal.
- Edit nama kategori dengan klik tombol **Edit**.
- Hapus kategori (pastikan tidak ada produk yang menggunakan kategori tersebut, atau hapus produk terlebih dahulu).

### 4.5 Menyetujui Produk (Pending Products)
- Buka **Admin Panel → Pending Products**.
- Admin harus menyetujui produk baru sebelum muncul di toko.
- Klik **Approve** untuk mengaktifkan produk, atau **Reject** untuk menolak (status menjadi inactive).

### 4.6 Melihat Semua Pesanan
- Buka **Admin Panel → Semua Pesanan**.
- Gunakan filter status (Semua / Pending / Paid / Cancelled) untuk menyaring.
- Tampilan menggunakan card per pesanan (detail lengkap).
- Klik **Export CSV** untuk mengunduh daftar pesanan sesuai filter.

---

## 5. Notifikasi & Email

- **Notifikasi in-app** muncul di ikon lonceng navbar. Notifikasi baru ditandai dengan badge merah.
- **Email** dikirim untuk:
  - Registrasi berhasil
  - Pesanan berhasil dibuat
  - Pembayaran dikonfirmasi
- Pastikan konfigurasi SMTP di file `.env` sudah benar agar email terkirim.

---

## 6. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Lupa password | Hubungi admin (belum ada fitur reset password). Admin dapat mengubah role dan password via database. |
| Upload produk gagal (file terlalu besar) | Batas ukuran file produk 30 MB, cover 5 MB. Kompres file atau minta admin menaikkan `upload_max_filesize` di php.ini. |
| Pembayaran via Midtrans error 401 | Pastikan `.env` menggunakan **Sandbox Key** (diawali `SB-Mid-server...`) dan mode `MIDTRANS_IS_PRODUCTION=false`. |
| Email tidak terkirim | Periksa konfigurasi SMTP di `.env`, gunakan App Password Gmail, pastikan port 587 terbuka. |
| Status produk pending terus | Admin harus menyetujui produk di menu **Pending Products**. |
| Halaman tidak responsif di HP | Coba refresh dengan hard reload (Ctrl+F5) atau hapus cache browser. Pastikan menggunakan Chrome/Edge dengan Device Toolbar. |
| Keranjang tidak muncul | Pastikan session aktif dan tidak ada error di console browser (F12 → Console). |

---

**Selamat berjualan dan berbelanja!**  
Jika ada pertanyaan lebih lanjut, hubungi tim pengembang.

*Manual ini terakhir diperbarui: Juni 2026.*