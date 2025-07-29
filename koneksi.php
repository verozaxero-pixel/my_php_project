<?php
$host = "localhost";
$user = "root";        // sesuaikan dengan user database kamu
$password = "";        // sesuaikan jika pakai password
$database = "protech_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
