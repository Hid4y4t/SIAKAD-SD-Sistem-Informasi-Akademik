<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Ambil data dari form input
$nis_siswa = $_POST['nis_siswa'];
$id_nominal = $_POST['id_nominal'];

if (empty($nis_siswa) || empty($id_nominal)) {
    die("NIS Siswa dan Nominal Dana Pengembangan harus diisi.");
}

// Cari ID siswa berdasarkan NIS
$siswaQuery = "SELECT id_siswa FROM siswa WHERE nis = ?";
$stmtSiswa = $mysqli->prepare($siswaQuery);
$stmtSiswa->bind_param("s", $nis_siswa);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();

if ($resultSiswa->num_rows === 0) {
    die("Siswa dengan NIS $nis_siswa tidak ditemukan.");
}

$siswaData = $resultSiswa->fetch_assoc();
$id_siswa = $siswaData['id_siswa'];

// Cek apakah siswa ini bebas dari dana pengembangan
$checkExemptQuery = "SELECT id_bebas FROM siswa_bebas_dana_pengembangan WHERE id_siswa = ?";
$stmtExempt = $mysqli->prepare($checkExemptQuery);
$stmtExempt->bind_param("i", $id_siswa);
$stmtExempt->execute();
$resultExempt = $stmtExempt->get_result();

if ($resultExempt->num_rows > 0) {

    echo "<script>alert('Siswa ini bebas dari dana pengembangan, tidak dapat ditambahkan.'); window.location.href = 'setting_pengembangan.php';</script>";
}

// Ambil nominal tagihan untuk angkatan dari tabel dana_pengembangan_nominal
$nominalQuery = "SELECT jumlah_total FROM dana_pengembangan_nominal WHERE id_nominal = ?";
$stmtNominal = $mysqli->prepare($nominalQuery);
$stmtNominal->bind_param("i", $id_nominal);
$stmtNominal->execute();
$resultNominal = $stmtNominal->get_result();
$nominalData = $resultNominal->fetch_assoc();

if (!$nominalData) {
    die("Nominal dana pengembangan tidak ditemukan.");
}

$total_tagihan = $nominalData['jumlah_total'];

// Tambahkan data ke tabel dana_pengembangan
$insertQuery = "INSERT INTO dana_pengembangan (id_siswa, id_nominal, total_tagihan, jumlah_terbayar, status) VALUES (?, ?, ?, 0, 'Belum Lunas')";
$stmtInsert = $mysqli->prepare($insertQuery);
$stmtInsert->bind_param("iid", $id_siswa, $id_nominal, $total_tagihan);

if ($stmtInsert->execute()) {
    echo "<script>alert('Data siswa berhasil ditambahkan ke dana pengembangan.'); window.location.href = 'setting_pengembangan.php';</script>";
} else {
    echo "Terjadi kesalahan saat menambahkan data: " . $mysqli->error;
}
?>
