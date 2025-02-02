<?php
session_start(); // Memulai sesi

// Hapus semua data sesi
session_unset();

// Hancurkan sesi
session_destroy();

// Arahkan pengguna kembali ke halaman login
header("Location: index.php"); // Ubah 'login.php' dengan jalur halaman login Anda
exit();
?>
