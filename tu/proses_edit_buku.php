<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

// Ambil data dari form
$id_buku = $_POST['id_buku'];
$kelas = $_POST['kelas'];
$jenis_buku = $_POST['jenis_buku'];
$harga = $_POST['harga'];

// Pastikan data yang diperlukan tidak kosong
if (empty($id_buku) || empty($kelas) || empty($jenis_buku) || empty($harga)) {
    echo "Data tidak lengkap.";
    exit;
}

// Query untuk memperbarui data di tabel buku
$query = "UPDATE buku SET kelas = ?, jenis_buku = ?, harga = ? WHERE id_buku = ?";
$stmt = $mysqli->prepare($query);

if ($stmt) {
    $stmt->bind_param("ssdi", $kelas, $jenis_buku, $harga, $id_buku);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Jika data berhasil diperbarui
        $_SESSION['success_message'] = "Buku berhasil diperbarui.";
        header("Location: setting_buku.php");
    } else {
        // Jika terjadi kesalahan saat memperbarui data
        $_SESSION['error_message'] = "Gagal memperbarui buku.";
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
