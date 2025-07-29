<?php
session_start();
$conn = new mysqli("localhost", "root", "", "protech_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pesan_instruktur = "";

// Tambah instruktur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_instruktur'])) {
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $nomor_hp = $_POST['nomor_hp'];
    $keahlian = $_POST['keahlian'];
    $alamat = $_POST['alamat'];

    $stmt = $conn->prepare("INSERT INTO instruktur (nama_lengkap, email, nomor_hp, keahlian, alamat, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $nama, $email, $nomor_hp, $keahlian, $alamat);

    if ($stmt->execute()) {
        $pesan_instruktur = "✅ Instruktur berhasil ditambahkan.";
    } else {
        $pesan_instruktur = "❌ Gagal tambah instruktur: " . $conn->error;
    }
    $stmt->close();
}

// Edit instruktur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_instruktur'])) {
    $id = $_POST['id_instruktur'];
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $nomor_hp = $_POST['nomor_hp'];
    $keahlian = $_POST['keahlian'];
    $alamat = $_POST['alamat'];

    $stmt = $conn->prepare("UPDATE instruktur SET nama_lengkap=?, email=?, nomor_hp=?, keahlian=?, alamat=? WHERE id_instruktur=?");
    $stmt->bind_param("sssssi", $nama, $email, $nomor_hp, $keahlian, $alamat, $id);

    if ($stmt->execute()) {
        $pesan_instruktur = "✅ Instruktur berhasil diupdate.";
    } else {
        $pesan_instruktur = "❌ Gagal update instruktur: " . $conn->error;
    }
    $stmt->close();
}

// Hapus instruktur
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $conn->query("DELETE FROM instruktur WHERE id_instruktur = $id_hapus");
    header("Location: data_instruktur.php");
    exit;
}

// Ambil data instruktur
$instruktur_result = $conn->query("SELECT * FROM instruktur ORDER BY id_instruktur DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Data Instruktur - Protech Academy</title>
  <link rel="stylesheet" href="dashboard_admin.css" />
  <style>
    table input, table textarea, table select {
      width: 100%;
      box-sizing: border-box;
    }
    table td form {
      margin: 0;
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
      <li><a href="data_instruktur.php" class="active">Data Instruktur</a></li>
      <li><a href="data_siswa.php">Data Siswa</a></li>
      <li><a href="logout.php" class="logout">Keluar</a></li>
    </ul>
  </div>

  <div class="main-content">
    <div class="header">
      <h1>Data Instruktur</h1>
      <p><?= $pesan_instruktur ?: "Isi atau lihat data instruktur di bawah ini." ?></p>
    </div>

    <div class="table-section">
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

    <div class="table-section">
      <h2>📋 Daftar Instruktur</h2>
      <table border="1" cellpadding="5" cellspacing="0">
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Nomor HP</th>
          <th>Keahlian</th>
          <th>Alamat</th>
          <th>Created At</th>
          <th>Aksi</th>
        </tr>
        <?php while ($row = $instruktur_result->fetch_assoc()): ?>
        <tr>
          <form method="POST">
            <input type="hidden" name="id_instruktur" value="<?= $row['id_instruktur'] ?>">
            <td><?= $row['id_instruktur'] ?></td>
            <td><input type="text" name="nama_lengkap" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required></td>
            <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required></td>
            <td><input type="text" name="nomor_hp" value="<?= htmlspecialchars($row['nomor_hp']) ?>" required></td>
            <td><input type="text" name="keahlian" value="<?= htmlspecialchars($row['keahlian']) ?>" required></td>
            <td><textarea name="alamat" required><?= htmlspecialchars($row['alamat']) ?></textarea></td>
            <td><?= $row['created_at'] ?></td>
            <td>
              <button type="submit" name="edit_instruktur">💾</button>
              <a href="data_instruktur.php?hapus=<?= $row['id_instruktur'] ?>" onclick="return confirm('Yakin hapus instruktur ini?')">🗑️</a>
            </td>
          </form>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</body>
</html>
