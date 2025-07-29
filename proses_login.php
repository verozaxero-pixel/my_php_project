<?php
session_start();
include 'koneksi.php';

$username = trim($_POST['username']);
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Jika password di DB sudah hash pakai password_hash()
    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

        if ($user['role'] === 'admin') {
            header("Location: dashboard_admin.php");
            exit;
        } else {
            header("Location: dashboard_user.php");
            exit;
        }
    } else {
        header("Location: login.php?error=wrongpassword");
        exit;
    }
} else {
    header("Location: login.php?error=notfound");
    exit;
}
