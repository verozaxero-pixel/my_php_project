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

// Tambah data siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_siswa'])) {
    $nama = $_POST['nama_lengkap'];
    $nis = $_POST['nis'];
    $nomor_hp = $_POST['nomor_hp'];
    $nama_orang_tua = $_POST['nama_orang_tua'];
    $alamat = $_POST['alamat'];

    $stmt = $conn->prepare("INSERT INTO siswa (nama_lengkap, nis, nomor_hp, nama_orang_tua, alamat, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $nama, $nis, $nomor_hp, $nama_orang_tua, $alamat);

    if ($stmt->execute()) {
        $pesan = "✅ Data siswa berhasil ditambahkan.";
    } else {
        $pesan = "❌ Gagal tambah data siswa: " . $conn->error;
    }
    $stmt->close();
}

// Edit data siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_siswa'])) {
    $id = $_POST['id_siswa'];
    $nama = $_POST['nama_lengkap'];
    $nis = $_POST['nis'];
    $nomor_hp = $_POST['nomor_hp'];
    $nama_orang_tua = $_POST['nama_orang_tua'];
    $alamat = $_POST['alamat'];

    $stmt = $conn->prepare("UPDATE siswa SET nama_lengkap=?, nis=?, nomor_hp=?, nama_orang_tua=?, alamat=? WHERE id_siswa=?");
    $stmt->bind_param("sssssi", $nama, $nis, $nomor_hp, $nama_orang_tua, $alamat, $id);

    if ($stmt->execute()) {
        $pesan = "✅ Data siswa berhasil diupdate.";
    } else {
        $pesan = "❌ Gagal update data siswa: " . $conn->error;
    }
    $stmt->close();
}

// Hapus data siswa
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $conn->query("DELETE FROM siswa WHERE id_siswa = $id_hapus");
    header("Location: data_siswa.php");
    exit;
}

// Ambil data siswa terbaru
$siswa_result = $conn->query("SELECT * FROM siswa ORDER BY id_siswa DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Data Siswa - Protech Academy</title>
  <link rel="stylesheet" href="dashboard_admin.css" />
  <style>
    /* Sedikit styling tambahan agar form rapi */
    form input, form textarea {
      margin: 5px 0;
      padding: 8px;
      width: 100%;
      box-sizing: border-box;
    }
    form button {
      margin-top: 8px;
      padding: 10px 20px;
    }
    table input, table textarea, table select {
      width: 100%;
      box-sizing: border-box;
    }
    .message-box {
      background: #d4edda;
      padding: 10px;
      border: 1px solid #c3e6cb;
      margin-bottom: 20px;
      color: #155724;
      border-radius: 4px;
    }
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
      <li><a href="data_siswa.php" class="active">Data Siswa</a></li>
      <li><a href="logout.php" class="logout">Keluar</a></li>
    </ul>
  </div>

  <div class="main-content">
    <div class="header">
      <h1>Data Siswa</h1>
      <?php if ($pesan): ?>
        <div class="message-box"><?= $pesan ?></div>
      <?php else: ?>
        <p>Isi atau lihat data siswa di bawah ini.</p>
      <?php endif; ?>
    </div>

    <div class="table-section">
      <h2>➕ Tambah Data Siswa</h2>
      <form method="POST">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required />
        <input type="text" name="nis" placeholder="NIS" required />
        <input type="text" name="nomor_hp" placeholder="Nomor HP" required />
        <input type="text" name="nama_orang_tua" placeholder="Nama Orang Tua" required />
        <textarea name="alamat" placeholder="Alamat" required></textarea>
        <button type="submit" name="tambah_siswa">Tambah</button>
      </form>
    </div>

    <div class="table-section">
      <h2>📋 Daftar Siswa</h2>
      <table border="1" cellpadding="5" cellspacing="0">
        <tr>
          <th>ID</th>
          <th>Nama Lengkap</th>
          <th>NIS</th>
          <th>Nomor HP</th>
          <th>Nama Orang Tua</th>
          <th>Alamat</th>
          <th>Created At</th>
          <th>Aksi</th>
        </tr>
        <?php while ($row = $siswa_result->fetch_assoc()): ?>
        <tr>
          <form method="POST">
            <input type="hidden" name="id_siswa" value="<?= $row['id_siswa'] ?>">
            <td><?= $row['id_siswa'] ?></td>
            <td><input type="text" name="nama_lengkap" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required></td>
            <td><input type="text" name="nis" value="<?= htmlspecialchars($row['nis']) ?>" required></td>
            <td><input type="text" name="nomor_hp" value="<?= htmlspecialchars($row['nomor_hp']) ?>" required></td>
            <td><input type="text" name="nama_orang_tua" value="<?= htmlspecialchars($row['nama_orang_tua']) ?>" required></td>
            <td><textarea name="alamat" required><?= htmlspecialchars($row['alamat']) ?></textarea></td>
            <td><?= $row['created_at'] ?></td>
            <td>
              <button type="submit" name="edit_siswa">💾</button>
              <a href="data_siswa.php?hapus=<?= $row['id_siswa'] ?>" onclick="return confirm('Yakin hapus data siswa ini?')">🗑️</a>
            </td>
          </form>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</body>
</html>
