<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Periksa apakah data yang diperlukan sudah diterima melalui POST
if (!isset($_POST['nis_siswa']) || !isset($_POST['jenis_potongan']) || !isset($_POST['jumlah'])) {
    echo "Data tidak lengkap. Pastikan semua data sudah diisi!";
    exit;
}

// Menghubungkan ke database
require '../koneksi/koneksi.php';

$nis_siswa = $_POST['nis_siswa'];
$jenis_potongan = $_POST['jenis_potongan'];
$jumlah = $_POST['jumlah'];
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : null;

// Ambil ID siswa berdasarkan NIS yang dimasukkan
$query = "SELECT id_siswa FROM siswa WHERE nis = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $nis_siswa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Siswa dengan NIS tersebut tidak ditemukan!";
    exit;
}

$row = $result->fetch_assoc();
$id_siswa = $row['id_siswa'];

$stmt->close();

// Siapkan query untuk menambahkan data potongan
$query = "INSERT INTO potongan_spp (id_siswa, jenis_potongan, jumlah, keterangan, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    echo "Gagal menyiapkan statement: " . $mysqli->error;
    exit;
}

// Bind parameter dan eksekusi query
$stmt->bind_param("iids", $id_siswa, $jenis_potongan, $jumlah, $keterangan);

if ($stmt->execute()) {
    // Redirect kembali ke halaman daftar potongan dengan pesan sukses
    $_SESSION['success_message'] = "Potongan berhasil ditambahkan.";
    header("Location: potongan.php"); // Ganti dengan halaman yang sesuai
} else {
    echo "Gagal menambahkan data potongan: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
