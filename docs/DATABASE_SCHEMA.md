# 📊 Database Schema – Digital Product Store

Dokumentasi skema database untuk aplikasi **Digital Product Store**.  
Database ini menggunakan **MySQL / MariaDB** dengan charset `utf8mb4` untuk mendukung emoji dan karakter multibyte.

---

## 📁 Tabel & Relasi

![ERD Diagram](https://via.placeholder.com/800x400?text=ERD+Diagram+Digital+Product+Store)  
*(Gambarkan ERD sederhana di sini atau tambahkan gambar terpisah)*

---

## 📋 Daftar Tabel

| No | Nama Tabel | Deskripsi |
|----|------------|-----------|
| 1  | `users` | Menyimpan data pengguna (admin, seller, buyer) |
| 2  | `categories` | Kategori produk digital |
| 3  | `products` | Data produk digital (file, cover, harga, status) |
| 4  | `orders` | Header pesanan (transaksi) |
| 5  | `order_items` | Detail item dalam setiap pesanan |
| 6  | `reviews` | Review & rating produk dari pembeli |
| 7  | `wishlists` | Daftar keinginan (wishlist) pembeli |
| 8  | `notifications` | Notifikasi in-app untuk semua pengguna |
| 9  | `payments` | Riwayat pembayaran (terpisah dari orders) |

---

## 🔧 Detail Tabel

### 1. `users` – Data Pengguna

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID unik user |
| `name` | VARCHAR(100) NOT NULL | Nama lengkap |
| `email` | VARCHAR(100) UNIQUE NOT NULL | Email (login) |
| `password` | VARCHAR(255) NOT NULL | Hash password (bcrypt) |
| `role` | ENUM('admin','seller','buyer') DEFAULT 'buyer' | Peran pengguna |
| `remember_token` | VARCHAR(255) NULL | Token untuk "Remember Me" |
| `created_at` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu pendaftaran |

**Indeks:** `email` (UNIQUE), `role`  
**Relasi:** `products.seller_id` → `users.id` ; `orders.buyer_id` → `users.id` ; `reviews.user_id` → `users.id`

---

### 2. `categories` – Kategori Produk

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID kategori |
| `name` | VARCHAR(50) NOT NULL | Nama kategori (misal: Ebook, Template) |
| `created_at` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |

**Indeks:** `name`  
**Relasi:** `products.category_id` → `categories.id`

---

### 3. `products` – Produk Digital

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID produk |
| `seller_id` | INT NOT NULL | Penjual (FK ke `users.id`) |
| `category_id` | INT NOT NULL | Kategori (FK ke `categories.id`) |
| `name` | VARCHAR(200) NOT NULL | Nama produk |
| `description` | TEXT | Deskripsi produk |
| `price` | DECIMAL(10,2) NOT NULL | Harga produk |
| `file_path` | VARCHAR(255) NOT NULL | Path file produk digital |
| `cover_image` | VARCHAR(255) | Path cover / gambar produk |
| `status` | ENUM('pending','active','inactive') DEFAULT 'pending' | Status produk |
| `created_at` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu ditambahkan |

**Indeks:** `seller_id`, `category_id`, `status`  
**Relasi:** `seller_id` → `users.id` ; `category_id` → `categories.id`

---

### 4. `orders` – Pesanan

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID pesanan |
| `buyer_id` | INT NOT NULL | Pembeli (FK ke `users.id`) |
| `total_amount` | DECIMAL(10,2) NOT NULL | Total nilai pesanan |
| `shipping_address` | TEXT NOT NULL | Alamat pengiriman |
| `city` | VARCHAR(100) NOT NULL | Kota pengiriman |
| `postal_code` | VARCHAR(20) | Kode pos |
| `payment_method` | VARCHAR(50) NOT NULL | Metode pembayaran (bank_transfer, qris, midtrans) |
| `shipping_cost` | DECIMAL(10,2) DEFAULT 0 | Ongkos kirim (simulasi) |
| `payment_proof` | VARCHAR(255) NULL | Nama file bukti transfer (manual) |
| `payment_token` | VARCHAR(255) NULL | Token dari Midtrans |
| `payment_url` | TEXT NULL | URL pembayaran Midtrans |
| `status` | ENUM('pending','paid','cancelled') DEFAULT 'pending' | Status pesanan |
| `order_date` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Tanggal pesanan |

**Indeks:** `buyer_id`, `status`, `order_date`  
**Relasi:** `buyer_id` → `users.id`

---

### 5. `order_items` – Detail Item Pesanan

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID item |
| `order_id` | INT NOT NULL | ID pesanan (FK ke `orders.id`) |
| `product_id` | INT NOT NULL | ID produk (FK ke `products.id`) |
| `quantity` | INT NOT NULL | Jumlah produk |
| `price` | DECIMAL(10,2) NOT NULL | Harga per unit (saat checkout) |

**Relasi:** `order_id` → `orders.id` ; `product_id` → `products.id`

---

### 6. `reviews` – Review & Rating

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID review |
| `product_id` | INT NOT NULL | Produk (FK ke `products.id`) |
| `user_id` | INT NOT NULL | Pembeli (FK ke `users.id`) |
| `order_id` | INT NOT NULL | Pesanan terkait (FK ke `orders.id`) |
| `rating` | INT NOT NULL CHECK (1–5) | Bintang rating |
| `comment` | TEXT | Komentar |
| `photo` | VARCHAR(255) | Foto review (opsional) |
| `created_at` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu review |

**Indeks:** `product_id`, `user_id`, `order_id`  
**Unique:** `(product_id, user_id, order_id)`  
**Relasi:** semua foreign key ke tabel terkait

---

### 7. `wishlists` – Wishlist

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID wishlist |
| `user_id` | INT NOT NULL | Pembeli (FK ke `users.id`) |
| `product_id` | INT NOT NULL | Produk (FK ke `products.id`) |
| `created_at` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu ditambahkan |

**Unique:** `(user_id, product_id)`  
**Relasi:** `user_id` → `users.id` ; `product_id` → `products.id`

---

### 8. `notifications` – Notifikasi In-App

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID notifikasi |
| `user_id` | INT NOT NULL | Penerima (FK ke `users.id`) |
| `type` | VARCHAR(50) NOT NULL | Tipe notifikasi (welcome, order_created, etc.) |
| `title` | VARCHAR(255) NOT NULL | Judul notifikasi |
| `message` | TEXT NOT NULL | Pesan notifikasi |
| `link` | VARCHAR(255) NULL | Tautan tujuan |
| `is_read` | BOOLEAN DEFAULT FALSE | Status baca |
| `created_at` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |

**Indeks:** `(user_id, is_read)`  
**Relasi:** `user_id` → `users.id`

---

### 9. `payments` – Riwayat Pembayaran

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | ID pembayaran |
| `order_id` | INT NOT NULL | Order terkait (FK ke `orders.id`) |
| `payment_method` | VARCHAR(50) NOT NULL | Metode pembayaran |
| `amount` | DECIMAL(10,2) NOT NULL | Jumlah dibayar |
| `proof` | VARCHAR(255) NULL | File bukti (manual) |
| `status` | ENUM('pending','success','failed','refunded') DEFAULT 'pending' | Status pembayaran |
| `transaction_id` | VARCHAR(100) NULL | ID transaksi dari payment gateway |
| `payment_date` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Tanggal pembayaran |
| `updated_at` | TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Update terakhir |

**Indeks:** `order_id`, `status`  
**Relasi:** `order_id` → `orders.id`

---

## 🔗 Relasi Antar Tabel (Ringkasan)

```mermaid
erDiagram
    users ||--o{ products : "seller"
    users ||--o{ orders : "buyer"
    users ||--o{ reviews : "author"
    users ||--o{ wishlists : "owner"
    users ||--o{ notifications : "recipient"
    categories ||--o{ products : "category"
    products ||--o{ order_items : "item"
    products ||--o{ reviews : "product"
    products ||--o{ wishlists : "product"
    orders ||--o{ order_items : "contains"
    orders ||--o{ payments : "payment"
    orders ||--o{ reviews : "order"