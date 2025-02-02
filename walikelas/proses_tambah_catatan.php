<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Periksa apakah data form telah dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa = $_POST['id_siswa'];
    $id_kelas = $_POST['id_kelas'];
    $id_wali_kelas = $_POST['id_wali_kelas'];
    $permasalahan = $_POST['permasalahan'];
    $solusi = $_POST['solusi'];
    $catatan_tambahan = $_POST['catatan_tambahan'] ?? '';
    $dilaporkan_kepada = $_POST['dilaporkan_kepada'];
    $tanggal = date('Y-m-d'); // Tanggal saat ini

    // Validasi data yang masuk
    if (empty($id_siswa) || empty($id_kelas) || empty($id_wali_kelas) || empty($permasalahan) || empty($solusi) || empty($dilaporkan_kepada)) {
        echo "<script>alert('Semua kolom wajib diisi.'); window.history.back();</script>";
        exit;
    }

    // Simpan data ke database
    $query = "INSERT INTO catatan_wali_kelas (id_siswa, id_kelas, id_wali_kelas, tanggal, permasalahan, solusi, catatan_tambahan, dilaporkan_kepada) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param(
        "iiisssss",
        $id_siswa,
        $id_kelas,
        $id_wali_kelas,
        $tanggal,
        $permasalahan,
        $solusi,
        $catatan_tambahan,
        $dilaporkan_kepada
    );

    if ($stmt->execute()) {
        echo "<script>alert('Catatan berhasil ditambahkan.'); window.location.href='catatan_walikelas.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan catatan: {$stmt->error}'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Metode pengiriman tidak valid.'); window.history.back();</script>";
    exit;
}
?>
