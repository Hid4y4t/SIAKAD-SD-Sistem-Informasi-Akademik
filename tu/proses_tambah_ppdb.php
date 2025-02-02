<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Cek apakah data yang diperlukan tersedia
if (!isset($_POST['nis_siswa']) || !isset($_POST['id_nominal'])) {
    die("NIS Siswa dan Angkatan harus diisi.");
}

// Ambil data dari formulir
$nis_siswa = $_POST['nis_siswa'];
$id_nominal = intval($_POST['id_nominal']);

// Query untuk mendapatkan `id_siswa` berdasarkan `nis_siswa`
$querySiswa = "SELECT id_siswa FROM siswa WHERE nis = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("s", $nis_siswa);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();

if ($resultSiswa->num_rows == 0) {
    die("Siswa dengan NIS tersebut tidak ditemukan.");
}

$id_siswa = $resultSiswa->fetch_assoc()['id_siswa'];

// Cek apakah siswa sudah ada di tabel `ppdb_pembayaran`
$queryCekPembayaran = "SELECT * FROM ppdb_pembayaran WHERE id_siswa = ?";
$stmtCekPembayaran = $mysqli->prepare($queryCekPembayaran);
$stmtCekPembayaran->bind_param("i", $id_siswa);
$stmtCekPembayaran->execute();
$resultCekPembayaran = $stmtCekPembayaran->get_result();

if ($resultCekPembayaran->num_rows > 0) {
    die("Siswa ini sudah memiliki data pembayaran PPDB.");
}

// Ambil jumlah tagihan dari tabel `ppdb_nominal` berdasarkan `id_nominal`
$queryNominal = "SELECT jumlah_total FROM ppdb_nominal WHERE id_nominal = ?";
$stmtNominal = $mysqli->prepare($queryNominal);
$stmtNominal->bind_param("i", $id_nominal);
$stmtNominal->execute();
$resultNominal = $stmtNominal->get_result();

if ($resultNominal->num_rows == 0) {
    die("Nominal untuk angkatan tersebut tidak ditemukan.");
}

$total_tagihan = $resultNominal->fetch_assoc()['jumlah_total'];

// Tambahkan data ke tabel `ppdb_pembayaran`
$queryTambahPembayaran = "
    INSERT INTO ppdb_pembayaran (id_siswa, id_nominal, total_tagihan, jumlah_terbayar, status)
    VALUES (?, ?, ?, 0, 'Belum Lunas')
";
$stmtTambahPembayaran = $mysqli->prepare($queryTambahPembayaran);
$stmtTambahPembayaran->bind_param("iid", $id_siswa, $id_nominal, $total_tagihan);

if ($stmtTambahPembayaran->execute()) {
    header("Location: setting_ppdb.php?status=success");
} else {
    echo "Gagal menambahkan data pembayaran PPDB.";
}

$stmtSiswa->close();
$stmtCekPembayaran->close();
$stmtNominal->close();
$stmtTambahPembayaran->close();
$mysqli->close();
?>
