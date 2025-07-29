<?php
session_start();
$conn = new mysqli("localhost", "root", "", "protech_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek login user
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$pesan = "";

// Tambah instruktur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_instruktur'])) {
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $nomor_hp = $_POST['nomor_hp'];
    $keahlian = $_POST['keahlian'];
    $alamat = $_POST['alamat'];

    $stmt = $conn->prepare("INSERT INTO instruktur (nama_lengkap, email, nomor_hp, keahlian, alamat, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $nama, $email, $nomor_hp, $keahlian, $alamat);
    $stmt->execute();
    $pesan = "✅ Instruktur berhasil ditambahkan.";
    $stmt->close();
}

// Tambah siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_siswa'])) {
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $nomor_hp = $_POST['nomor_hp'];
    $alamat = $_POST['alamat'];

    $stmt = $conn->prepare("INSERT INTO siswa (nama_lengkap, email, nomor_hp, alamat, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $nama, $email, $nomor_hp, $alamat);
    $stmt->execute();
    $pesan = "✅ Siswa berhasil ditambahkan.";
    $stmt->close();
}

// Ambil data
$instruktur_result = $conn->query("SELECT * FROM instruktur ORDER BY id_instruktur DESC");
$siswa_result = $conn->query("SELECT * FROM siswa ORDER BY id_siswa DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard User - Protech Academy</title>
  <link rel="stylesheet" href="dashboard_admin_v2.css" />
  <style>
    .sidebar .logo-container {
      text-align: center;
      padding: 20px 0 10px;
      border-bottom: 1px solid #34495e;
    }
    .sidebar .logo-container img {
      width: 60px;
      height: auto;
      display: block;
      margin: 0 auto 10px;
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
      <li><a href="dashboard_user.php">Dashboard</a></li>
      <!-- tombol tambah instruktur dan siswa di sidebar dihapus sesuai permintaan -->
      <li><a href="logout.php" class="logout">Keluar</a></li>
    </ul>
  </div>

  <div class="main-content">
    <div class="header">
      <h1>Selamat datang User <?= htmlspecialchars($_SESSION['username']) ?></h1>
      <p>Silakan tambahkan instruktur dan siswa melalui panel berikut.</p>
    </div>

    <?php if($pesan): ?>
      <div class="message-box"><?= $pesan ?></div>
    <?php endif; ?>

    <!-- Tambah Instruktur -->
    <div class="table-section" id="form-instruktur">
      <h2>➕ Tambah Instruktur</h2>
      <form method="POST">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="text" name="nomor_hp" placeholder="Nomor HP" required />
        <input type="text" name="keahlian" placeholder="Keahlian" required />
        <textarea name="alamat" placeholder="Alamat" required></textarea>
        <button type="submit" name="tambah_instruktur">Tambah</button>
      </form>
    </div>

    <!-- Tambah Siswa -->
    <div class="table-section" id="form-siswa">
      <h2>➕ Tambah Siswa</h2>
      <form method="POST">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="text" name="nomor_hp" placeholder="Nomor HP" required />
        <textarea name="alamat" placeholder="Alamat" required></textarea>
        <button type="submit" name="tambah_siswa">Tambah</button>
      </form>
    </div>

    <!-- Daftar Instruktur -->
    <div class="table-section">
      <h2>📋 Daftar Instruktur</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Nomor HP</th>
          <th>Keahlian</th>
          <th>Alamat</th>
          <th>Created At</th>
        </tr>
        <?php while ($row = $instruktur_result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_instruktur'] ?></td>
          <td><?= $row['nama_lengkap'] ?></td>
          <td><?= $row['email'] ?></td>
          <td><?= $row['nomor_hp'] ?></td>
          <td><?= $row['keahlian'] ?></td>
          <td><?= $row['alamat'] ?></td>
          <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>

    <!-- Daftar Siswa -->
    <div class="table-section">
      <h2>📋 Daftar Siswa</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Nomor HP</th>
          <th>Alamat</th>
          <th>Created At</th>
        </tr>
        <?php while ($row = $siswa_result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_siswa'] ?></td>
          <td><?= $row['nama_lengkap'] ?></td>
          <td><?= $row['email'] ?></td>
          <td><?= $row['nomor_hp'] ?></td>
          <td><?= $row['alamat'] ?></td>
          <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</body>
</html>
