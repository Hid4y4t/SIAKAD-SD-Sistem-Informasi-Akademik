<?php
require '../koneksi/koneksi.php';

$idBeasiswa = $_GET['id_beasiswa_js'];

$query = $mysqli->prepare("SELECT potongan AS jumlah FROM jenis_beasiswa WHERE id_beasiswa_js = ?");
$query->bind_param("i", $idBeasiswa);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>
