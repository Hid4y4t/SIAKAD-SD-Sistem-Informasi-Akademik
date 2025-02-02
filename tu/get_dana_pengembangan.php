<?php
require_once '../koneksi/koneksi.php';

$id_siswa = $_GET['id_siswa'];
$query = "SELECT s.nama_siswa, s.kelas, dp.total_tagihan, dp.jumlah_terbayar, dp.status 
          FROM dana_pengembangan dp
          JOIN siswa s ON dp.id_siswa = s.id_siswa
          WHERE dp.id_siswa = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode($result ? $result : ['error' => 'Data not found']);
?>
