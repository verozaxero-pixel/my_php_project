<?php
session_start();

// Fungsi cek apakah user sudah login dan sesuai role
function cek_login($required_role) {
    if (!isset($_SESSION['username'])) {
        // Belum login, redirect ke login
        header("Location: login.php");
        exit;
    }
    if ($_SESSION['role'] !== $required_role) {
        // Role tidak sesuai, redirect atau tampilkan pesan
        echo "Akses ditolak. Anda bukan $required_role.";
        exit;
    }
}
?>
