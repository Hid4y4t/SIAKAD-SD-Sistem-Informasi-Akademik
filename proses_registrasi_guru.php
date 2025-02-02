<?php
require 'koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_guru = $_POST['nama_guru'];
    $email_guru = $_POST['email_guru'];
    $username = $_POST['username'];
    $telepon_guru = $_POST['telepon_guru'];
    $alamat_guru = $_POST['alamat_guru'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jabatan = $_POST['jabatan'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi password dan confirm password
    if ($password !== $confirm_password) {
        die('Password dan konfirmasi password tidak sesuai.');
    }

    // Hashing password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Cek apakah username atau email sudah digunakan
    $query = "SELECT * FROM guru WHERE username = ? OR email_guru = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $username, $email_guru);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Username atau email sudah terdaftar.");
    }

    // Menyimpan data guru baru
    $insert_query = "INSERT INTO guru (nama_guru, email_guru, username, telepon_guru, alamat_guru, jenis_kelamin, jabatan, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $mysqli->prepare($insert_query);
    $stmt_insert->bind_param("ssssssss", $nama_guru, $email_guru, $username, $telepon_guru, $alamat_guru, $jenis_kelamin, $jabatan, $hashed_password);

    if ($stmt_insert->execute()) {
        header("Location: login_guru.php?success=1");
    } else {
        die("Gagal mendaftarkan guru.");
    }
}
?>
