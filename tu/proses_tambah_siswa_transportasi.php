<?php
session_start();
require_once '../koneksi/koneksi.php';

// Periksa apakah data dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data NIS dan ID Zona dari form
    $nis = isset($_POST['nis']) ? trim($_POST['nis']) : '';
    $id_zona = isset($_POST['id_zona']) ? intval($_POST['id_zona']) : 0;

    // Validasi input
    if (empty($nis) || $id_zona <= 0) {
        $_SESSION['error'] = "NIS atau Zona tidak valid.";
        header("Location: setting_transportasi.php");
        exit;
    }

    // Dapatkan ID siswa berdasarkan NIS
    $querySiswa = "SELECT id_siswa FROM siswa WHERE nis = ?";
    $stmtSiswa = $mysqli->prepare($querySiswa);
    $stmtSiswa->bind_param("s", $nis);
    $stmtSiswa->execute();
    $resultSiswa = $stmtSiswa->get_result();

    if ($resultSiswa->num_rows === 0) {
        $_SESSION['error'] = "Siswa dengan NIS tersebut tidak ditemukan.";
        header("Location: setting_transportasi.php");
        exit;
    }

    $siswaData = $resultSiswa->fetch_assoc();
    $id_siswa = $siswaData['id_siswa'];

    // Cek apakah siswa sudah terdaftar di transportasi
    $checkQuery = "SELECT * FROM transportasi_pembayaran WHERE id_siswa = ?";
    $stmtCheck = $mysqli->prepare($checkQuery);
    $stmtCheck->bind_param("i", $id_siswa);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        $_SESSION['error'] = "Siswa ini sudah terdaftar dalam transportasi.";
        header("Location: setting_transportasi.php");
        exit;
    }

    // Proses insert siswa ke tabel transportasi_pembayaran
    $insertQuery = "INSERT INTO transportasi_pembayaran (id_siswa, id_zona) VALUES (?, ?)";
    $stmtInsert = $mysqli->prepare($insertQuery);

    if (!$stmtInsert) {
        // Jika prepare gagal, tampilkan pesan error
        $_SESSION['error'] = "Error preparing statement: " . $mysqli->error;
        header("Location: setting_transportasi.php");
        exit;
    }

    $stmtInsert->bind_param("ii", $id_siswa, $id_zona);

    if ($stmtInsert->execute()) {
        $_SESSION['success'] = "Siswa berhasil ditambahkan ke transportasi.";
    } else {
        // Jika execute gagal, tampilkan pesan error
        $_SESSION['error'] = "Error: " . $stmtInsert->error;
    }

    header("Location: setting_transportasi.php");
    exit;
} else {
    // Redirect jika halaman diakses tanpa POST
    header("Location: setting_transportasi.php");
    exit;
}
