<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Memastikan bahwa ada data id_potongan yang dikirim melalui POST
if (!isset($_POST['id_potongan'])) {
    echo "ID potongan tidak ditemukan!";
    exit;
}

// Menghubungkan ke database
require '../koneksi/koneksi.php';

$id_potongan = $_POST['id_potongan'];

// Siapkan query untuk menghapus data potongan
$query = "DELETE FROM potongan_spp WHERE id_potongan = ?";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    echo "Gagal menyiapkan statement: " . $mysqli->error;
    exit;
}

// Bind parameter dan eksekusi query
$stmt->bind_param("i", $id_potongan);

if ($stmt->execute()) {
    // Redirect kembali ke halaman daftar potongan dengan pesan sukses
    $_SESSION['success_message'] = "Potongan berhasil dihapus.";
    header("Location: potongan.php"); // Ganti dengan halaman yang sesuai
} else {
    echo "Gagal menghapus data potongan: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
