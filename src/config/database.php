<?php
// src/config/database.php

// Konfigurasi database
// Untuk XAMPP: host='localhost', user='root', pass=''
// Untuk Docker: host='db', user='digiuser', pass='digipass'
$host = 'localhost';
$dbname = 'digital_product_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>