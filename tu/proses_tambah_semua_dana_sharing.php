<?php
require_once '../koneksi/koneksi.php';

$currentDate = date("Y-m-d");

// Ambil data siswa yang tidak berada dalam tabel siswa_bebas_dana_sharing atau sudah melebihi tanggal pembebasan
$querySiswa = "
    SELECT s.id_siswa, s.kelas, s.angkatan
    FROM siswa s
    LEFT JOIN siswa_bebas_dana_sharing sb ON s.id_siswa = sb.id_siswa AND (sb.tanggal_selesai IS NULL OR sb.tanggal_selesai >= ?)
    WHERE sb.id_siswa IS NULL";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("s", $currentDate);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();

// Ambil nominal tagihan dari tabel dana_sharing_nominal berdasarkan kelas dan angkatan siswa
while ($siswa = $resultSiswa->fetch_assoc()) {
    $kelas = $siswa['kelas'];
    $angkatan = $siswa['angkatan'];

    $queryNominal = "SELECT jumlah_tagihan FROM dana_sharing_nominal WHERE kelas = ? AND angkatan = ?";
    $stmtNominal = $mysqli->prepare($queryNominal);
    $stmtNominal->bind_param("ss", $kelas, $angkatan);
    $stmtNominal->execute();
    $resultNominal = $stmtNominal->get_result();
    
    if ($nominal = $resultNominal->fetch_assoc()) {
        $jumlah_tagihan = $nominal['jumlah_tagihan'];

        // Tambahkan ke tabel dana_sharing
        $insertQuery = "INSERT INTO dana_sharing (id_siswa, jumlah_tagihan, tanggal_pembayaran, status) VALUES (?, ?, NULL, 'Belum Bayar')";
        $insertStmt = $mysqli->prepare($insertQuery);
        $insertStmt->bind_param("id", $siswa['id_siswa'], $jumlah_tagihan);
        $insertStmt->execute();
        $insertStmt->close();
    }
    $stmtNominal->close();
}

$stmtSiswa->close();
$mysqli->close();

echo json_encode(["message" => "Data dana sharing berhasil ditambahkan untuk semua siswa yang sesuai."]);
?>
