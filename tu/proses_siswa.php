<?php
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = $_POST['nis'];
    $nama_siswa = $_POST['nama_siswa'];
    $email_siswa = $_POST['email_siswa'];
    $telepon_siswa = $_POST['telepon_siswa'];
    $alamat_siswa = $_POST['alamat_siswa'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $kelas = $_POST['kelas'];
    $angkatan = $_POST['angkatan'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $status = $_POST['status'];

    $stmt = $mysqli->prepare("INSERT INTO siswa (nis, nama_siswa, email_siswa, telepon_siswa, alamat_siswa, tanggal_lahir, kelas, angkatan, jenis_kelamin, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssi", $nis, $nama_siswa, $email_siswa, $telepon_siswa, $alamat_siswa, $tanggal_lahir, $kelas, $angkatan, $jenis_kelamin, $status);

    if ($stmt->execute()) {
        header("Location: siswa.php?success=1");
    } else {
        header("Location: siswa.php?error=1");
    }
}
?>
