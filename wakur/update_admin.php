<?php
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_admin = $_POST['id_admin'];
    $nama_admin = $_POST['nama_admin'];
    $email_admin = $_POST['email_admin'];
    $username_admin = $_POST['username_admin'];
    $telepon_admin = $_POST['telepon_admin'];
    $jabatan = $_POST['jabatan'];

    $query = "UPDATE admin SET nama_admin = ?, email_admin = ?, username_admin = ?, telepon_admin = ?, jabatan = ? WHERE id_admin = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssssi", $nama_admin, $email_admin, $username_admin, $telepon_admin, $jabatan, $id_admin);

    if ($stmt->execute()) {
        header('Location: guru_karyawan.php?success=1');
    } else {
        echo "Gagal memperbarui data.";
    }
}
?>
