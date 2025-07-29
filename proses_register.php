<?php
session_start();
include "koneksi.php";

$username = trim($_POST['username']);
$password = $_POST['password'];
$nama_lengkap = trim($_POST['nama_lengkap']);
$role = $_POST['role'];

// Cek apakah username sudah ada
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    // Username sudah ada
    header("Location: register.php?status=gagal");
    exit;
}

// Hash password sebelum disimpan
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user baru
$query_insert = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password_hash', '$nama_lengkap', '$role')";
$insert = mysqli_query($conn, $query_insert);

if ($insert) {
    header("Location: register.php?status=sukses");
} else {
    header("Location: register.php?status=error");
}
exit;
