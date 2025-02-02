<?php
require_once '../koneksi/koneksi.php';

$id_dana_pengembangan = $_POST['id_dana_pengembangan'];
$query = "DELETE FROM dana_pengembangan WHERE id_dana_pengembangan = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_dana_pengembangan);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Data successfully deleted.";
} else {
    $response['success'] = false;
    $response['message'] = "Failed to delete data.";
}

echo json_encode($response);
?>
