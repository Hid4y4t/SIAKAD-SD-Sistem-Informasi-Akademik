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
    // Ambil data dari form
    $id_tagihan = intval($_POST['id_tagihan']);
    $angkatan = trim($_POST['angkatan']);
    $jumlah_tagihan = trim($_POST['jumlah_tagihan']);
    $keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : null;

    // Validasi data
    if (empty($angkatan) || empty($jumlah_tagihan) || $id_tagihan <= 0) {
        $_SESSION['error_message'] = "Angkatan, jumlah tagihan, dan ID tagihan harus diisi dengan benar.";
        header("Location: tagihan_spp.php"); // Redirect kembali ke halaman tagihan SPP
        exit;
    }

    // Query untuk memperbarui data di tabel `tagihan_spp`
    $query = "UPDATE tagihan_spp SET angkatan = ?, jumlah_tagihan = ?, keterangan = ? WHERE id_tagihan = ?";
    $stmt = $mysqli->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("sdsi", $angkatan, $jumlah_tagihan, $keterangan, $id_tagihan);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Tagihan berhasil diperbarui.";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui tagihan: " . $stmt->error;
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
