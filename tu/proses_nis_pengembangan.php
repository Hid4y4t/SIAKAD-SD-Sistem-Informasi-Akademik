<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_POST['nis']) || empty(trim($_POST['nis']))) {
    echo "<script>alert('NIS Siswa harus diisi.'); window.history.back();</script>";
    exit;
}

$nis = trim($_POST['nis']);

// Cek apakah NIS ada di tabel siswa
$query = "SELECT id_siswa FROM siswa WHERE nis = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Siswa dengan NIS tersebut tidak ditemukan.'); window.history.back();</script>";
    exit;
}

$siswa = $result->fetch_assoc();
$id_siswa = $siswa['id_siswa'];

// Redirect ke halaman pembayaran dana pengembangan siswa dengan ID siswa
header("Location: pembayaran_dana_pengembangan_siswa.php?id_siswa=$id_siswa");
exit;
?>
