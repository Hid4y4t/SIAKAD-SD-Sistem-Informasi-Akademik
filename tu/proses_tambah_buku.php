<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

// Ambil data dari form
$kelas = $_POST['kelas'];
$jenis_buku = $_POST['jenis_buku'];
$harga = $_POST['harga'];

// Pastikan data yang diperlukan tidak kosong
if (empty($kelas) || empty($jenis_buku) || empty($harga)) {
    echo "Data tidak lengkap.";
    exit;
}

// Query untuk menambahkan data ke tabel buku
$query = "INSERT INTO buku (kelas, jenis_buku, harga) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($query);

if ($stmt) {
    $stmt->bind_param("ssd", $kelas, $jenis_buku, $harga);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Jika data berhasil ditambahkan
        $_SESSION['success_message'] = "Buku berhasil ditambahkan.";
        header("Location: setting_buku.php");
    } else {
        // Jika terjadi kesalahan saat menambahkan data
        $_SESSION['error_message'] = "Gagal menambahkan buku.";
        header("Location: setting_buku.php");
    }

    $stmt->close();
} else {
    // Jika terjadi kesalahan pada query
    $_SESSION['error_message'] = "Kesalahan pada query: " . $mysqli->error;
    header("Location: setting_buku.php");
}

$mysqli->close();
?>
