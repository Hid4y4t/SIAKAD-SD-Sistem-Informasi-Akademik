<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Validasi input dari form
$nis = $_POST['nis'] ?? '';
$alasan_bebas = $_POST['alasan_bebas'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? null;

// Cek apakah semua data yang diperlukan sudah diisi
if (empty($nis) || empty($alasan_bebas) || empty($tanggal_mulai)) {
    header("Location: setting_ppdb.php?status=error&message=Data tidak lengkap.");
    exit;
}

// Ambil ID siswa berdasarkan NIS
$querySiswa = "SELECT id_siswa FROM siswa WHERE nis = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("s", $nis);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();

if ($resultSiswa->num_rows === 0) {
    header("Location: setting_ppdb.php?status=error&message=Siswa tidak ditemukan.");
    exit;
}

$id_siswa = $resultSiswa->fetch_assoc()['id_siswa'];

// Cek apakah siswa sudah ada di tabel `siswa_bebas_ppdb`
$queryCheck = "SELECT id_bebas FROM siswa_bebas_ppdb WHERE id_siswa = ?";
$stmtCheck = $mysqli->prepare($queryCheck);
$stmtCheck->bind_param("i", $id_siswa);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    header("Location: setting_ppdb.php?status=error&message=Siswa sudah dibebaskan dari pembayaran PPDB.");
    exit;
}

// Insert data ke tabel `siswa_bebas_ppdb`
$queryInsert = "
    INSERT INTO siswa_bebas_ppdb (id_siswa, alasan_bebas, tanggal_mulai, tanggal_selesai)
    VALUES (?, ?, ?, ?)
";
$stmtInsert = $mysqli->prepare($queryInsert);
$stmtInsert->bind_param("isss", $id_siswa, $alasan_bebas, $tanggal_mulai, $tanggal_selesai);

if ($stmtInsert->execute()) {
    header("Location: setting_ppdb.php?status=success&message=Siswa berhasil dibebaskan dari pembayaran PPDB.");
} else {
    header("Location: setting_ppdb.php?status=error&message=Gagal menambahkan siswa bebas.");
}

// Tutup koneksi
$stmtInsert->close();
$stmtSiswa->close();
$stmtCheck->close();
$mysqli->close();
