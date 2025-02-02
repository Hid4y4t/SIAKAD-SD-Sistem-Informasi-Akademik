<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Cek apakah NIS telah dikirim melalui form
if (isset($_POST['nis'])) {
    $nis = $_POST['nis'];

    // Query untuk mencari siswa berdasarkan NIS
    $query = "SELECT id_siswa FROM siswa WHERE nis = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah siswa ditemukan
    if ($result->num_rows > 0) {
        $siswa = $result->fetch_assoc();
        $id_siswa = $siswa['id_siswa'];

        // Redirect ke halaman pembayaran dengan id_siswa
        header("Location: pembayaran_ppdb_siswa.php?id_siswa=" . $id_siswa);
        exit;
    } else {
        // Jika NIS tidak ditemukan, kembali ke halaman sebelumnya dengan pesan error
        $_SESSION['error'] = "NIS tidak ditemukan atau siswa masuk dalam kategori bebas biayay PPDB.";
        header("Location: pembayaran_ppdb_tp.php");
        exit;
    }
} else {
    // Jika NIS tidak dikirimkan, kembali ke halaman sebelumnya
    $_SESSION['error'] = "Masukkan NIS untuk mencari data siswa.";
    header("Location: pembayaran_ppdb_tp.php");
    exit;
}
?>
