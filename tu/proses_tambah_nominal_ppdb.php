<?php
session_start();
require_once '../koneksi/koneksi.php';

// Pastikan user memiliki hak akses
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    die("Anda tidak memiliki izin untuk mengakses halaman ini.");
}

// Pastikan data dari form telah diterima dengan benar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $angkatan = $_POST['angkatan'];
    $jumlah_total = $_POST['jumlah_total'];

    // Pastikan data yang diterima tidak kosong
    if (empty($angkatan) || empty($jumlah_total)) {
        die("Harap isi semua field.");
    }

    // Query untuk menambahkan data nominal PPDB ke tabel
    $query = "INSERT INTO ppdb_nominal (angkatan, jumlah_total) VALUES (?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sd", $angkatan, $jumlah_total);

    if ($stmt->execute()) {
        header("Location: setting_ppdb.php?status=success");
        exit;
    } else {
        die("Gagal menambahkan nominal PPDB.");
    }

    $stmt->close();
}

$mysqli->close();
?>
