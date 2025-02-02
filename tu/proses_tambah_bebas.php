<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Ambil data dari form
$nis = $_POST['nis'];
$alasan_bebas = $_POST['alasan_bebas'];
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];

// Validasi input
if (empty($nis) || empty($alasan_bebas) || empty($tanggal_mulai)) {
    echo "Harap lengkapi semua bidang yang wajib diisi.";
    exit;
}

// Cek apakah siswa dengan NIS ini sudah ada dalam tabel siswa
$querySiswa = "SELECT id_siswa FROM siswa WHERE nis = ?";
$stmt = $mysqli->prepare($querySiswa);
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Siswa dengan NIS ini tidak ditemukan.";
    exit;
}

$id_siswa = $result->fetch_assoc()['id_siswa'];

// Cek apakah siswa ini sudah ada di tabel siswa_bebas_dana_pengembangan
$queryCheck = "SELECT id_bebas FROM siswa_bebas_dana_pengembangan WHERE id_siswa = ?";
$stmtCheck = $mysqli->prepare($queryCheck);
$stmtCheck->bind_param("i", $id_siswa);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    echo "Siswa ini sudah terdaftar sebagai siswa bebas dana pengembangan.";
    exit;
}

// Menambahkan data ke tabel siswa_bebas_dana_pengembangan
$queryInsert = "
    INSERT INTO siswa_bebas_dana_pengembangan (id_siswa, alasan_bebas, tanggal_mulai, tanggal_selesai)
    VALUES (?, ?, ?, ?)
";
$stmtInsert = $mysqli->prepare($queryInsert);
$stmtInsert->bind_param("isss", $id_siswa, $alasan_bebas, $tanggal_mulai, $tanggal_selesai);

if ($stmtInsert->execute()) {
    echo "success"; // Menampilkan pesan sukses
} else {
    echo "Gagal menambahkan data: " . $stmtInsert->error;
}

$stmt->close();
$stmtCheck->close();
$stmtInsert->close();
$mysqli->close();
?>
