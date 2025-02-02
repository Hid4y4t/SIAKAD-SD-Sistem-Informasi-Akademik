<?php
session_start();
require_once '../koneksi/koneksi.php';

// Pastikan user memiliki hak akses
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus data ini.']);
    exit;
}

// Mendapatkan id_nominal dari request
$id_nominal = isset($_POST['id_nominal']) ? intval($_POST['id_nominal']) : 0;

if ($id_nominal <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID Nominal tidak valid.']);
    exit;
}

// Query untuk menghapus data nominal PPDB berdasarkan id_nominal
$query = "DELETE FROM ppdb_nominal WHERE id_nominal = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_nominal);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus data.']);
}

$stmt->close();
$mysqli->close();
?>
