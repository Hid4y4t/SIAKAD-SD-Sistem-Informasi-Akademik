<?php
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_guru = $_POST['id_guru'];
    $nama_guru = $_POST['nama_guru'];
    $email_guru = $_POST['email_guru'];
    $telepon_guru = $_POST['telepon_guru'];
    $alamat_guru = $_POST['alamat_guru'];
    $jabatan = $_POST['jabatan'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    $query = "UPDATE guru SET nama_guru = ?, email_guru = ?, telepon_guru = ?, alamat_guru = ?, jabatan = ?, jenis_kelamin = ? WHERE id_guru = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssssssi", $nama_guru, $email_guru, $telepon_guru, $alamat_guru, $jabatan, $jenis_kelamin, $id_guru);

    if ($stmt->execute()) {
        header('Location: guru_karyawan.php?success=1');
    } else {
        echo "Gagal memperbarui data.";
    }
}
?>
