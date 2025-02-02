<?php
require_once '../koneksi/koneksi.php'; // Pastikan file koneksi sudah ada

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_admin = $_POST['nama_admin'];
    $email = $_POST['email_admin'];
    $username = $_POST['username_admin'];
    $telepon = $_POST['telepon_admin'];
    $password = $_POST['password_admin'];
    $confirm_password = $_POST['confirm_password_admin'];
    $jabatan = $_POST['jabatan'];

    // Validasi form: cek apakah password dan konfirmasi password cocok
    if ($password !== $confirm_password) {
        echo "Password dan konfirmasi password tidak cocok.";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah email atau username sudah ada di database
    $query_check = "SELECT id_admin FROM admin WHERE email_admin = ? OR username_admin = ?";
    $stmt = executeQuery($query_check, [$email, $username], 'ss');
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Jika email atau username sudah terdaftar
        echo "Email atau username sudah digunakan, silakan gunakan yang lain.";
        exit;
    } else {
        // Simpan data admin ke database
        $query = "INSERT INTO admin (nama_admin, username_admin, email_admin, password_admin, telepon_admin, jabatan) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $result = executeQuery($query, [$nama_admin, $username, $email, $hashed_password, $telepon, $jabatan], 'ssssss');

        if ($result) {
            echo "Registrasi berhasil! Silakan login.";
            header("Location: guru_karyawan.php");
            exit;
        } else {
            echo "Registrasi gagal. Silakan coba lagi.";
        }
    }
}
?>
