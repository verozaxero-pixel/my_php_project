<?php
session_start();
if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
        exit;
    } elseif ($_SESSION['role'] === 'user') {
        header("Location: dashboard_user.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Sistem Informasi Protech</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>
  <div class="login-container">
    <img src="logo.png" alt="Logo Protech" class="logo" />
    
    <h2>Login Protech</h2>

    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] === 'wrongpassword') {
            echo '<p style="color:red;">Password salah!</p>';
        } elseif ($_GET['error'] === 'notfound') {
            echo '<p style="color:red;">Username tidak ditemukan!</p>';
        }
    }
    ?>

    <form action="proses_login.php" method="post">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Masuk</button>
    </form>

    <p style="margin-top: 15px; color: #00c9ff;">
      Belum punya akun? <a href="register.php" style="color: #00c9ff; text-decoration: none;">Silahkan Register</a>
    </p>

    <p class="back-link"><a href="index.php">← Kembali ke Beranda</a></p>
  </div>
</body>
</html>
