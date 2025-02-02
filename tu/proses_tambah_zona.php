<?php
session_start();

// Check if the user is logged in and has the role 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_zona = trim($_POST['nama_zona']);
    $harga_per_trip = floatval($_POST['harga_per_trip']);

    // Validate form inputs
    if (empty($nama_zona) || $harga_per_trip <= 0) {
        $_SESSION['error'] = "Nama zona dan harga per trip harus diisi dengan benar.";
        header("Location: setting_transportasi.php");
        exit;
    }

    // Prepare SQL to insert new zone
    $query = "INSERT INTO zona_transportasi (nama_zona, harga_per_trip) VALUES (?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sd", $nama_zona, $harga_per_trip);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Zona transportasi berhasil ditambahkan.";
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menambahkan zona transportasi: " . $mysqli->error;
    }

    // Redirect back to the settings page
    header("Location: setting_transportasi.php");
    exit;
} else {
    $_SESSION['error'] = "Permintaan tidak valid.";
    header("Location: setting_transportasi.php");
    exit;
}
?>
