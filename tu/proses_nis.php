<?php
session_start();
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis']);
    
    // Cek apakah NIS ada di tabel siswa
    $query = "SELECT id_siswa FROM siswa WHERE nis = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_siswa);
        $stmt->fetch();
        header("Location: bayar_shering.php?id_siswa=" . $id_siswa);
        exit;
    } else {
        $_SESSION['error_message'] = "NIS tidak ditemukan. Silakan periksa kembali.";
        header("Location: pembayaran_dana_sharing.php"); // Redirect kembali ke halaman utama jika gagal
        exit;
    }
    $stmt->close();
}
?>
