<?php
require_once '../koneksi/koneksi.php';

$id_nominal = $_POST['id_nominal'];
$angkatan = $_POST['angkatan'];
$jumlah_total = $_POST['jumlah_total'];

$query = "UPDATE dana_pengembangan_nominal SET angkatan = ?, jumlah_total = ? WHERE id_nominal = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("sii", $angkatan, $jumlah_total, $id_nominal);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Nominal berhasil diubah.";
} else {
    $response['success'] = false;
    $response['message'] = "Gagal mengubah nominal.";
}
echo json_encode($response);
?>
