<?php
require_once '../koneksi/koneksi.php';

if (isset($_POST['nis'], $_POST['alasan_bebas'], $_POST['tanggal_mulai'])) {
    $nis = trim($_POST['nis']);
    $alasanBebas = trim($_POST['alasan_bebas']);
    $tanggalMulai = $_POST['tanggal_mulai'];
    $tanggalSelesai = !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : null;

    // Cek apakah NIS ada di tabel siswa
    $querySiswa = "SELECT id_siswa FROM siswa WHERE nis = ?";
    $stmt = $mysqli->prepare($querySiswa);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // NIS ditemukan, ambil id_siswa
        $row = $result->fetch_assoc();
        $idSiswa = $row['id_siswa'];

        // Simpan data ke tabel siswa_bebas_dana_sharing
        $queryInsert = "INSERT INTO siswa_bebas_dana_sharing (id_siswa, alasan_bebas, tanggal_mulai, tanggal_selesai) VALUES (?, ?, ?, ?)";
        $stmtInsert = $mysqli->prepare($queryInsert);
        $stmtInsert->bind_param("isss", $idSiswa, $alasanBebas, $tanggalMulai, $tanggalSelesai);

        if ($stmtInsert->execute()) {
            echo 'success';
        } else {
            echo 'Gagal menambahkan data.';
        }
    } else {
        echo 'NIS tidak ditemukan di database.';
    }
} else {
    echo 'Data tidak lengkap.';
}
?>
