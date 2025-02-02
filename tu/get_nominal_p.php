<?php
require_once '../koneksi/koneksi.php';

$id_nominal = $_GET['id'] ?? 0;
$query = "SELECT * FROM dana_pengembangan_nominal WHERE id_nominal = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_nominal);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode($result ? $result : ['error' => 'Data not found']);
?>
