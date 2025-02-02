<?php
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_beasiswa = $_POST['nama_beasiswa'];
    $keterangan = $_POST['keterangan'];
    $potongan = $_POST['potongan'];

    $stmt = $mysqli->prepare("INSERT INTO jenis_beasiswa (nama_beasiswa, keterangan, potongan) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $nama_beasiswa, $keterangan, $potongan);
    $stmt->execute();

    header("Location: beasiswa.php"); // Redirect ke halaman sebelumnya
    exit;
}
?>
