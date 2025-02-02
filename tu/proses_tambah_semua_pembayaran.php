<?php
session_start();

require_once '../koneksi/koneksi.php';

// Cek apakah pengguna memiliki akses yang benar
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Query untuk mengambil semua siswa dengan kelas terkait buku
$querySiswa = "SELECT id_siswa, kelas FROM siswa";
$resultSiswa = $mysqli->query($querySiswa);

if (!$resultSiswa) {
    $_SESSION['error_message'] = "Gagal mengambil data siswa: " . $mysqli->error;
    header("Location: setting_buku.php");
    exit;
}

// Persiapkan query untuk mengambil buku berdasarkan kelas
$queryBuku = "SELECT id_buku, harga FROM buku WHERE kelas = ?";
$stmtBuku = $mysqli->prepare($queryBuku);

// Persiapkan query untuk memeriksa pembayaran yang sudah ada
$queryCheck = "SELECT COUNT(*) as count FROM buku_pembayaran WHERE id_siswa = ? AND id_buku = ?";
$stmtCheck = $mysqli->prepare($queryCheck);

// Persiapkan query untuk menambahkan data pembayaran buku
$queryInsert = "INSERT INTO buku_pembayaran (id_siswa, id_buku, total_tagihan, jumlah_terbayar, status) VALUES (?, ?, ?, 0, 'Belum Lunas')";
$stmtInsert = $mysqli->prepare($queryInsert);

// Loop untuk setiap siswa
while ($siswa = $resultSiswa->fetch_assoc()) {
    $id_siswa = $siswa['id_siswa'];
    $kelas = $siswa['kelas'];

    // Ambil semua buku yang sesuai dengan kelas siswa
    $stmtBuku->bind_param("s", $kelas);
    $stmtBuku->execute();
    $resultBuku = $stmtBuku->get_result();

    while ($buku = $resultBuku->fetch_assoc()) {
        $id_buku = $buku['id_buku'];
        $total_tagihan = $buku['harga'];

        // Cek apakah sudah ada pembayaran untuk buku ini dan siswa ini
        $stmtCheck->bind_param("ii", $id_siswa, $id_buku);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $check = $resultCheck->fetch_assoc();

        // Jika belum ada pembayaran untuk kombinasi id_siswa dan id_buku, tambahkan
        if ($check['count'] == 0) {
            $stmtInsert->bind_param("iid", $id_siswa, $id_buku, $total_tagihan);
            $stmtInsert->execute();
        }
    }
}

// Tutup semua statement setelah selesai
$stmtBuku->close();
$stmtCheck->close();
$stmtInsert->close();

// Selesai, kembali ke halaman utama
$_SESSION['success_message'] = "Data pembayaran buku berhasil ditambahkan untuk semua siswa.";
header("Location: setting_buku.php");

$mysqli->close();
?>
