<?php
// =============================================
// LOGOUT.PHP - Keluar dari Sistem
// =============================================

session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit();
?>