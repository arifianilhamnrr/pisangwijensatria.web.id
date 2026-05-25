<?php
// =============================================
// INDEX.PHP - Halaman Awal
// Otomatis arahkan ke login atau dashboard
// =============================================

session_start();

if (isset($_SESSION['id_user']) && isset($_SESSION['nama_user'])) {
    // Sudah login → langsung ke dashboard
    header("Location: dashboard.php");
} else {
    // Belum login → ke halaman login
    header("Location: login.php");
}
exit();
?>