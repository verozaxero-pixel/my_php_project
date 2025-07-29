<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - Sistem Informasi Protech</title>
  <link rel="stylesheet" href="register.css" />
</head>
<body>
  <div class="register-container">
    <img src="logo.png" alt="Logo Protech" class="logo" />
    
    <h2>Form Registrasi</h2>

    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'sukses') {
            echo '<p style="color:limegreen;">✅ Registrasi berhasil! Silakan login.</p>';
        } elseif ($_GET['status'] == 'gagal') {
            echo '<p style="color:red;">❌ Registrasi gagal! Username atau nama sudah digunakan.</p>';
        } elseif ($_GET['status'] == 'error') {
            echo '<p style="color:orange;">⚠️ Terjadi kesalahan saat menyimpan data. Coba lagi nanti.</p>';
        }
    }
    ?>

    <form action="proses_register.php" method="post">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required />
      <select name="role" required>
        <option value="">-- Pilih Role --</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>
      <button type="submit">Daftar</button>
    </form>

    <p style="margin-top: 15px; color: #00c9ff;">
      Sudah punya akun? <a href="login.php" style="color: #00c9ff; text-decoration: none;">Login di sini</a>
    </p>
    
    <p class="back-link"><a href="index.php">← Kembali ke Beranda</a></p>
  </div>
</body>
</html>
