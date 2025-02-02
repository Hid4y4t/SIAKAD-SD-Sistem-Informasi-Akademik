<?php
require_once '../koneksi/koneksi.php';

$angkatan = $_POST['angkatan'];
$jumlah_total = $_POST['jumlah_total'];

$query = "INSERT INTO dana_pengembangan_nominal (angkatan, jumlah_total) VALUES (?, ?)";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $angkatan, $jumlah_total);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Nominal berhasil ditambahkan.";
} else {
    $response['success'] = false;
    $response['message'] = "Gagal menambahkan nominal.";
}
echo json_encode($response);
?>
