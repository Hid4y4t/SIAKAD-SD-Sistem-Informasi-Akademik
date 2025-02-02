<?php
require_once '../koneksi/koneksi.php';
$mysqli->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");

$bulan = $_GET['bulan'] ?? null;
$id_kelas = $_GET['id_kelas'] ?? null;

if (!$bulan || !$id_kelas) {
    echo json_encode([]);
    exit;
}

$query = "
    SELECT DISTINCT tanggal 
    FROM jurnal_kelas 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? AND id_kelas = ? 
    ORDER BY tanggal ASC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $bulan, $id_kelas);
$stmt->execute();
$result = $stmt->get_result();

$tanggal_list = [];
while ($row = $result->fetch_assoc()) {
    $tanggal_list[] = $row;
}

echo json_encode($tanggal_list);
