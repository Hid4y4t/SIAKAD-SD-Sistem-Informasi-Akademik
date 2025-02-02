<?php
require_once '../koneksi/koneksi.php';

$kelas = $_POST['kelas'];
$angkatan = $_POST['angkatan'];
$semester = $_POST['semester'];
$jumlah_tagihan = $_POST['jumlah_tagihan'];
$keterangan = $_POST['keterangan'];

// Menambahkan data ke tabel dana_sharing_nominal
$query = "INSERT INTO dana_sharing_nominal (kelas, angkatan, semester, jumlah_tagihan, keterangan) VALUES (?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("sssds", $kelas, $angkatan, $semester, $jumlah_tagihan, $keterangan);

if ($stmt->execute()) {
    echo "Data dana sharing nominal berhasil ditambahkan.";
} else {
    echo "Gagal menambahkan data dana sharing nominal.";
}
$stmt->close();
$mysqli->close();
header("Location: setting_sharing.php");
exit;
?>
