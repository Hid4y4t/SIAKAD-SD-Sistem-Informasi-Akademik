<?php
require '../koneksi/koneksi.php';

// Periksa apakah ada parameter ID guru
if (!isset($_GET['id_guru'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'ID guru tidak ditemukan']);
    exit;
}

$id_guru = intval($_GET['id_guru']);

// Query untuk mendapatkan data guru
$query = "SELECT * FROM guru WHERE id_guru = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_guru);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Guru tidak ditemukan']);
}
?>
