<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

// Ambil data dari form
$nis = $_POST['nis'];
$id_buku = $_POST['id_buku'];

// Pastikan data yang diperlukan tidak kosong
if (empty($nis) || empty($id_buku)) {
    $_SESSION['error_message'] = "Data tidak lengkap. Pastikan NIS dan Buku telah dipilih.";
    header("Location: setting_buku.php");
    exit;
}

// Cari ID siswa berdasarkan NIS
$querySiswa = "SELECT id_siswa FROM siswa WHERE nis = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("s", $nis);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();

if ($resultSiswa->num_rows === 0) {
    $_SESSION['error_message'] = "Siswa dengan NIS tersebut tidak ditemukan.";
    header("Location: setting_buku.php");
    exit;
}

$rowSiswa = $resultSiswa->fetch_assoc();
$id_siswa = $rowSiswa['id_siswa'];
$stmtSiswa->close();

// Ambil harga buku berdasarkan ID buku
$queryBuku = "SELECT harga FROM buku WHERE id_buku = ?";
$stmtBuku = $mysqli->prepare($queryBuku);
$stmtBuku->bind_param("i", $id_buku);
$stmtBuku->execute();
$resultBuku = $stmtBuku->get_result();

if ($resultBuku->num_rows === 0) {
    $_SESSION['error_message'] = "Buku tersebut tidak ditemukan.";
    header("Location: setting_buku.php");
    exit;
}

$rowBuku = $resultBuku->fetch_assoc();
$total_tagihan = $rowBuku['harga'];
$stmtBuku->close();

// Masukkan data ke tabel buku_pembayaran
$queryPembayaran = "INSERT INTO buku_pembayaran (id_siswa, id_buku, total_tagihan, jumlah_terbayar, status) VALUES (?, ?, ?, 0, 'Belum Lunas')";
$stmtPembayaran = $mysqli->prepare($queryPembayaran);

if ($stmtPembayaran) {
    $stmtPembayaran->bind_param("iid", $id_siswa, $id_buku, $total_tagihan);
    $stmtPembayaran->execute();

    if ($stmtPembayaran->affected_rows > 0) {
        // Jika data berhasil ditambahkan
        $_SESSION['success_message'] = "Pembayaran buku berhasil ditambahkan.";
        header("Location: setting_buku.php");
    } else {
        // Jika terjadi kesalahan saat menambahkan data
        $_SESSION['error_message'] = "Gagal menambahkan pembayaran buku.";
        header("Location: setting_buku.php");
    }

    $stmtPembayaran->close();
} else {
    // Jika terjadi kesalahan pada query
    $_SESSION['error_message'] = "Kesalahan pada query: " . $mysqli->error;
    header("Location: setting_buku.php");
}

$mysqli->close();
?>
