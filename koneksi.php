<?php
// =============================================
// FILE KONEKSI DATABASE
// Pisang Wijen Satria - Sistem Persediaan
// =============================================
date_default_timezone_set('Asia/Jakarta');
$host     = "localhost";
$username = "pisangwijen";
$password = "YxnafJKmX2htfiay";
$database = "pisangwijen";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("
    <div style='font-family:sans-serif;padding:40px;text-align:center;'>
        <h2 style='color:#c0392b;'>&#10060; Koneksi Database Gagal</h2>
        <p style='color:#555;'>Pastikan XAMPP sudah dinyalakan dan MySQL sudah Running.</p>
        <p style='color:#888;font-size:13px;'>Error: " . mysqli_connect_error() . "</p>
    </div>
    ");
}

mysqli_set_charset($koneksi, "utf8mb4");

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>