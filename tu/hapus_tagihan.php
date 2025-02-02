<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

// Memastikan metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil ID tagihan dari permintaan POST
    $id_tagihan = intval($_POST['id_tagihan']);

    // Validasi ID tagihan
    if ($id_tagihan <= 0) {
        $_SESSION['error_message'] = "ID tagihan tidak valid.";
        header("Location: tagihan_spp.php");
        exit;
    }

    // Query untuk menghapus data dari tabel `tagihan_spp`
    $query = "DELETE FROM tagihan_spp WHERE id_tagihan = ?";
    $stmt = $mysqli->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $id_tagihan);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Tagihan berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus tagihan: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan pada query: " . $mysqli->error;
    }
} else {
    $_SESSION['error_message'] = "Metode request tidak valid.";
}

// Redirect kembali ke halaman tagihan SPP
header("Location: tagihan.php");
exit;
?>
