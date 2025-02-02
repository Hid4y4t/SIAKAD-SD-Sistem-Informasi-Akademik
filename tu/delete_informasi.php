<?php
session_start();
require_once '../koneksi/koneksi.php';

// Periksa apakah pengguna sudah login dan memiliki akses sebagai admin
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Ambil `id_informasi` dari URL dan pastikan ini adalah integer
$id_informasi = isset($_GET['id_informasi']) ? intval($_GET['id_informasi']) : 0;

if ($id_informasi > 0) {
    // Mulai transaksi untuk memastikan konsistensi data
    $mysqli->begin_transaction();

    try {
        // Hapus dari tabel `informasi_penerima` terlebih dahulu untuk menghindari masalah referensi
        $delete_penerima_query = "DELETE FROM informasi_penerima WHERE id_informasi = ?";
        $stmt = $mysqli->prepare($delete_penerima_query);
        $stmt->bind_param("i", $id_informasi);
        $stmt->execute();

        // Hapus dari tabel `informasi`
        $delete_informasi_query = "DELETE FROM informasi WHERE id_informasi = ?";
        $stmt = $mysqli->prepare($delete_informasi_query);
        $stmt->bind_param("i", $id_informasi);
        $stmt->execute();

        // Commit transaksi
        $mysqli->commit();

        echo "<script>alert('Informasi berhasil dihapus'); window.location.href = 'informasi.php';</script>";
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        $mysqli->rollback();
        echo "<script>alert('Gagal menghapus informasi: " . $e->getMessage() . "'); window.location.href = 'informasi.php';</script>";
    }
} else {
    echo "<script>alert('ID informasi tidak valid.'); window.location.href = 'informasi.php';</script>";
}

$mysqli->close();
?>
