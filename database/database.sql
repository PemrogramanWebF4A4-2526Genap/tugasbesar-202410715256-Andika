-- ============================================================
-- DATABASE: digital_product_store (Lengkap & Terupdate)
-- ============================================================

-- Hapus database jika sudah ada (untuk fresh install)
-- DROP DATABASE IF EXISTS digital_product_store;

-- Buat database baru
CREATE DATABASE IF NOT EXISTS digital_product_store;
USE digital_product_store;

-- ------------------------------------------------------------
-- 1. TABEL users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'seller', 'buyer') DEFAULT 'buyer',
    remember_token VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 2. TABEL categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 3. TABEL products
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    file_path VARCHAR(255) NOT NULL,          -- path file produk digital
    cover_image VARCHAR(255),                 -- cover gambar produk
    status ENUM('pending', 'active', 'inactive') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 4. TABEL orders
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20),
    payment_method VARCHAR(50) NOT NULL,       -- bank_transfer / qris / midtrans
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    payment_proof VARCHAR(255) NULL,           -- bukti transfer (manual)
    payment_token VARCHAR(255) NULL,           -- token Midtrans
    payment_url TEXT NULL,                     -- URL pembayaran Midtrans
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 5. TABEL order_items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,              -- harga per unit saat checkout
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 6. TABEL reviews
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT NOT NULL,                     -- review hanya bisa dari order yang sudah dibayar
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    photo VARCHAR(255),                        -- foto review (opsional)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (product_id, user_id, order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 7. TABEL wishlists
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 8. TABEL notifications (in-app)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,                -- welcome, order_created, payment_confirmed, dll
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL,                   -- link tujuan saat notifikasi diklik
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 9. TABEL payments (riwayat pembayaran terpisah)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,       -- bank_transfer / qris / midtrans
    amount DECIMAL(10,2) NOT NULL,
    proof VARCHAR(255) NULL,                  -- bukti pembayaran (manual)
    status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100) NULL,         -- dari Midtrans
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA AWAL (SEEDER)
-- ============================================================

-- Admin default (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Tambahan admin alternatif (optional)
INSERT INTO users (name, email, password, role) VALUES 
('Admin Baru', 'admin@kamu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Kategori default
INSERT INTO categories (name) VALUES 
('Ebook'), 
('Template'), 
('Software'), 
('Audio'), 
('Video');

-- Contoh produk (untuk testing, seller_id = 1 adalah admin, bisa diubah)
-- INSERT INTO products (seller_id, category_id, name, description, price, file_path, cover_image, status) 
-- VALUES (1, 1, 'Contoh Ebook', 'Deskripsi ebook contoh', 25000, 'dummy.pdf', 'dummy.jpg', 'active');

-- ============================================================
-- SELESAI
-- ============================================================