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
    $angkatan = trim($_POST['angkatan']);
    $jumlah_tagihan = trim($_POST['jumlah_tagihan']);
    $keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : null;

    // Validasi data
    if (empty($angkatan) || empty($jumlah_tagihan)) {
        $_SESSION['error_message'] = "Angkatan dan jumlah tagihan harus diisi.";
        header("Location: tagihan_spp.php"); // Redirect kembali ke halaman tagihan SPP
        exit;
    }

    // Query untuk memasukkan data ke tabel `tagihan_spp`
    $query = "INSERT INTO tagihan_spp (angkatan, jumlah_tagihan, keterangan) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("sds", $angkatan, $jumlah_tagihan, $keterangan);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Tagihan berhasil ditambahkan.";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan tagihan: " . $stmt->error;
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
