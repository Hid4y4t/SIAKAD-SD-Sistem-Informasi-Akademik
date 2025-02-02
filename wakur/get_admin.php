<?php
require '../koneksi/koneksi.php';

// Periksa apakah ada parameter ID admin
if (!isset($_GET['id_admin'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'ID admin tidak ditemukan']);
    exit;
}

$id_admin = intval($_GET['id_admin']);

// Query untuk mendapatkan data admin
$query = "SELECT * FROM admin WHERE id_admin = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Admin tidak ditemukan']);
}
?>
