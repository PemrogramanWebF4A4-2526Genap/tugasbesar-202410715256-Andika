<?php
// src/config/payment.php

// Load Midtrans SDK (via Composer)
require_once __DIR__ . '/../../vendor/autoload.php';

// ============================================================
// BACA FILE .env MANUAL (jika tidak menggunakan vlucas/phpdotenv)
// ============================================================
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// ============================================================
// AMBIL NILAI DARI ENVIRONMENT
// ============================================================
$serverKey = getenv('MIDTRANS_SERVER_KEY') ?: ($_ENV['MIDTRANS_SERVER_KEY'] ?? null);
$clientKey = getenv('MIDTRANS_CLIENT_KEY') ?: ($_ENV['MIDTRANS_CLIENT_KEY'] ?? null);
$isProduction = filter_var(
    getenv('MIDTRANS_IS_PRODUCTION') ?: ($_ENV['MIDTRANS_IS_PRODUCTION'] ?? false),
    FILTER_VALIDATE_BOOLEAN
);

// ============================================================
// VALIDASI (agar error terlihat jelas)
// ============================================================
if (empty($serverKey) || empty($clientKey)) {
    die("❌ ERROR: MIDTRANS_SERVER_KEY atau MIDTRANS_CLIENT_KEY tidak ditemukan di file .env. Pastikan file .env ada dan isinya benar.");
}

// ============================================================
// KONFIGURASI MIDTRANS
// ============================================================
\Midtrans\Config::$serverKey = $serverKey;
\Midtrans\Config::$clientKey = $clientKey;
\Midtrans\Config::$isProduction = $isProduction;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Opsional: jika ada masalah SSL di lokal (hanya untuk development)
// \Midtrans\Config::$curlOptions = [CURLOPT_SSL_VERIFYPEER => false];
?>