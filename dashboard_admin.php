<?php
session_start();
$conn = new mysqli("localhost", "root", "", "protech_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek login admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pesan = "";

// Tambah akun baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_akun'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password_hash, $role);

    if ($stmt->execute()) {
        $pesan = "✅ Akun baru berhasil ditambahkan.";
    } else {
        $pesan = "❌ Gagal tambah akun: " . $conn->error;
    }
    $stmt->close();
}

// Edit akun
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_akun'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $role, $id);

    if ($stmt->execute()) {
        $pesan = "✅ Akun berhasil diupdate.";
    } else {
        $pesan = "❌ Gagal update akun: " . $conn->error;
    }
    $stmt->close();
}

// Hapus akun
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $conn->query("DELETE FROM users WHERE id = $id_hapus");
    header("Location: dashboard_admin.php");
    exit;
}

// Statistik
$jumlah_siswa = $conn->query("SELECT COUNT(*) as total FROM siswa")->fetch_assoc()['total'];
$jumlah_instruktur = $conn->query("SELECT COUNT(*) as total FROM instruktur")->fetch_assoc()['total'];
$jumlah_akun = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

// Ambil data akun
$akun_result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Admin - Protech Academy</title>
  <link rel="stylesheet" href="dashboard_admin_v2.css" />
  <style>
    .sidebar .logo-container {
      text-align: center;
      padding: 15px;
      border-bottom: 1px solid #ddd;
    }
    .sidebar .logo-container img {
      max-width: 80px;
      height: auto;
      margin-bottom: 10px;
    }
    .message-box {
      margin: 15px 0;
      padding: 10px;
      border-radius: 5px;
      background-color: #dff0d8;
      color: #3c763d;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo-container">
      <img src="logo.png" alt="Logo Protech" />
    </div>
    <h2>PT Protech Academy Binjai</h2>
    <ul>
      <li><a href="dashboard_admin.php">Dashboard</a></li>
      <li><a href="data_instruktur.php">Data Instruktur</a></li>
      <li><a href="data_siswa.php">Data Siswa</a></li>
      <li><a href="logout.php" class="logout">Keluar</a></li>
    </ul>
  </div>

  <div class="main-content">
    <div class="header">
      <h1>Selamat datang Admin <?= htmlspecialchars($_SESSION['username']) ?></h1>
      <p>Gunakan panel di samping untuk mengelola data.</p>
    </div>

    <!-- Statistik -->
    <div class="stats">
  <div class="box blue">
    <h3><?= $jumlah_siswa ?></h3>
    <p>Jumlah Siswa</p>
  </div>
  <div class="box red">
    <h3><?= $jumlah_instruktur ?></h3>
    <p>Jumlah Instruktur</p>
  </div>
  <div class="box purple">
    <h3><?= $jumlah_akun ?></h3>
    <p>Jumlah Akun</p>
  </div>
</div>


    <!-- Pesan aksi -->
    <?php if($pesan): ?>
      <div class="message-box"><?= $pesan ?></div>
    <?php endif; ?>

    <!-- Form Tambah Akun -->
    <div class="table-section">
      <h2>➕ Tambah Akun Baru</h2>
      <form method="POST">
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <select name="role" required>
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>
        <button type="submit" name="tambah_akun">Tambah</button>
      </form>
    </div>

    <!-- List Akun -->
    <div class="table-section">
      <h2>📋 List Akun</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
        <?php while ($row = $akun_result->fetch_assoc()): ?>
        <tr>
          <form method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <td><?= $row['id'] ?></td>
            <td><input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required></td>
            <td>
              <select name="role" required>
                <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= $row['role'] === 'user' ? 'selected' : '' ?>>User</option>
              </select>
            </td>
            <td>
              <button type="submit" name="edit_akun">💾</button>
              <a href="dashboard_admin.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus akun ini?')">🗑️</a>
            </td>
          </form>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</body>
</html>
