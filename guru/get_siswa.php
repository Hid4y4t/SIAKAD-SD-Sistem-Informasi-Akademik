<?php
require_once '../koneksi/koneksi.php';

$id_kelas = $_GET['id_kelas'] ?? null;

if (!$id_kelas) {
    echo json_encode([]);
    exit;
}

$query = "SELECT id_siswa, nama_siswa FROM siswa WHERE kelas = (SELECT nama_kelas FROM kelas WHERE id_kelas = ?)";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_kelas);
$stmt->execute();
$result = $stmt->get_result();

$siswa = [];
while ($row = $result->fetch_assoc()) {
    $siswa[] = $row;
}

echo json_encode($siswa);
?>
