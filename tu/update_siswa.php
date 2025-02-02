<?php
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_siswa = $_POST['id_siswa'];
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

    $stmt = $mysqli->prepare("UPDATE siswa SET nis = ?, nama_siswa = ?, email_siswa = ?, telepon_siswa = ?, alamat_siswa = ?, tanggal_lahir = ?, kelas = ?,  angkatan =?, jenis_kelamin=?,  status = ? WHERE id_siswa = ?");
    $stmt->bind_param("sssssssissi", $nis, $nama_siswa, $email_siswa, $telepon_siswa, $alamat_siswa, $tanggal_lahir, $kelas, $angkatan, $jenis_kelamin, $status, $id_siswa);

    if ($stmt->execute()) {
        header("Location: siswa.php?update_success=1");
    } else {
        header("Location: siswa.php?update_error=1");
    }
}
?>
