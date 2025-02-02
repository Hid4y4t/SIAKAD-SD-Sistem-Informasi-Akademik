<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Validasi apakah NIS sudah diisi
if (!isset($_POST['nis']) || empty(trim($_POST['nis']))) {
    echo "<script>alert('NIS Siswa harus diisi.'); window.history.back();</script>";
    exit;
}

$nis = trim($_POST['nis']);

// Cek apakah NIS ada di tabel siswa
$query = "SELECT id_siswa, kelas FROM siswa WHERE nis = ?";
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
$kelas = $siswa['kelas'];

// Query untuk memeriksa apakah sudah ada data pembayaran buku untuk siswa ini
$queryCheck = "SELECT COUNT(*) as count FROM buku_pembayaran WHERE id_siswa = ?";
$stmtCheck = $mysqli->prepare($queryCheck);
$stmtCheck->bind_param("i", $id_siswa);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
$check = $resultCheck->fetch_assoc();

// Jika belum ada data pembayaran buku, tambahkan semua data buku sesuai kelas siswa
if ($check['count'] == 0) {
    // Ambil semua buku yang sesuai dengan kelas siswa
    $queryBuku = "SELECT id_buku, harga FROM buku WHERE kelas = ?";
    $stmtBuku = $mysqli->prepare($queryBuku);
    $stmtBuku->bind_param("s", $kelas);
    $stmtBuku->execute();
    $resultBuku = $stmtBuku->get_result();

    // Persiapkan statement untuk menambahkan data pembayaran buku
    $queryInsert = "INSERT INTO buku_pembayaran (id_siswa, id_buku, total_tagihan, jumlah_terbayar, status) VALUES (?, ?, ?, 0, 'Belum Lunas')";
    $stmtInsert = $mysqli->prepare($queryInsert);

    while ($buku = $resultBuku->fetch_assoc()) {
        $id_buku = $buku['id_buku'];
        $total_tagihan = $buku['harga'];

        // Tambahkan data pembayaran buku baru
        $stmtInsert->bind_param("iid", $id_siswa, $id_buku, $total_tagihan);
        $stmtInsert->execute();
    }

    $stmtBuku->close();
    if ($stmtInsert) {
        $stmtInsert->close();
    }
}

// Redirect ke halaman pembayaran buku untuk siswa dengan ID siswa
header("Location: pembayaran_buku_siswa.php?id_siswa=$id_siswa");
exit;
?>
