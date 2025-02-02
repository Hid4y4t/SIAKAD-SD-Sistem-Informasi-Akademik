<?php
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis_siswa = trim($_POST['nis_siswa']);
    $jumlah_tagihan = $_POST['jumlah_tagihan'];
    $status = $_POST['status'];

    // Cari ID siswa berdasarkan NIS
    $querySiswa = "SELECT id_siswa FROM siswa WHERE nis = ?";
    $stmtSiswa = $mysqli->prepare($querySiswa);
    $stmtSiswa->bind_param("s", $nis_siswa);
    $stmtSiswa->execute();
    $resultSiswa = $stmtSiswa->get_result();

    if ($resultSiswa->num_rows > 0) {
        $siswa = $resultSiswa->fetch_assoc();
        $id_siswa = $siswa['id_siswa'];

        // Masukkan data ke dalam dana_sharing
        $queryTambah = "INSERT INTO dana_sharing (id_siswa, jumlah_tagihan, status) VALUES (?, ?, ?)";
        $stmtTambah = $mysqli->prepare($queryTambah);
        $stmtTambah->bind_param("ids", $id_siswa, $jumlah_tagihan, $status);
        $stmtTambah->execute();

        if ($stmtTambah->affected_rows > 0) {
            header("Location: setting_sharing.php?success=1");
        } else {
            header("Location: setting_sharing.php?error=1");
        }
    } else {
        header("Location: setting_sharing.php?error=2");
    }
}
?>
