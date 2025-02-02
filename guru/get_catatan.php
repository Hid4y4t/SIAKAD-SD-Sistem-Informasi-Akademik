<?php
require_once '../koneksi/koneksi.php';

$id_siswa = $_GET['id_siswa'];
$id_guru = $_GET['id_guru'];
$id_kelas = $_GET['id_kelas'];

$query = "SELECT tanggal, catatan_guru FROM catatan_siswa WHERE id_siswa = ? AND id_guru = ? AND id_kelas = ? ORDER BY tanggal DESC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iii", $id_siswa, $id_guru, $id_kelas);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
