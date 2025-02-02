<?php
require_once '../koneksi/koneksi.php';

$id_nominal = $_POST['id_nominal'];
$query = "DELETE FROM dana_pengembangan_nominal WHERE id_nominal = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_nominal);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Nominal berhasil dihapus.";
} else {
    $response['success'] = false;
    $response['message'] = "Gagal menghapus nominal.";
}
echo json_encode($response);
?>
