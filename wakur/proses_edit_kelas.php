<?php
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_kelas = $_POST['id_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $kode_kelas = $_POST['kode_kelas'];
    $tingkat = $_POST['tingkat'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $wali_kelas = $_POST['wali_kelas'];

    // Perbarui data kelas
    $query = "UPDATE kelas SET nama_kelas = ?, kode_kelas = ?, tingkat = ?, tahun_ajaran = ?, wali_kelas = ? WHERE id_kelas = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssssii", $nama_kelas, $kode_kelas, $tingkat, $tahun_ajaran, $wali_kelas, $id_kelas);

    if ($stmt->execute()) {
        header("Location: kelas.php");
        exit;
    } else {
        echo "Gagal memperbarui data kelas.";
    }
} else {
    header("Location: kelas.php");
    exit;
}
