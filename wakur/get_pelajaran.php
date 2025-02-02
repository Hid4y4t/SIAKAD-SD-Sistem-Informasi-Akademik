<?php
require_once '../koneksi/koneksi.php';

// Periksa apakah parameter id_pelajaran diberikan
if (!isset($_GET['id_pelajaran']) || !is_numeric($_GET['id_pelajaran'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID pelajaran tidak valid.']);
    exit;
}

$id_pelajaran = $_GET['id_pelajaran'];

// Ambil data pelajaran
$query_pelajaran = "SELECT * FROM pelajaran WHERE id_pelajaran = ?";
$stmt_pelajaran = $mysqli->prepare($query_pelajaran);
$stmt_pelajaran->bind_param("i", $id_pelajaran);
$stmt_pelajaran->execute();
$result_pelajaran = $stmt_pelajaran->get_result();
$pelajaran = $result_pelajaran->fetch_assoc();

if (!$pelajaran) {
    http_response_code(404);
    echo json_encode(['error' => 'Data pelajaran tidak ditemukan.']);
    exit;
}

// Ambil data guru untuk dropdown
$query_guru = "SELECT id_guru, nama_guru FROM guru";
$result_guru = $mysqli->query($query_guru);
$guru_list = [];
while ($guru = $result_guru->fetch_assoc()) {
    $guru_list[] = $guru;
}

// Kirim data dalam format JSON
echo json_encode([
    'id_pelajaran' => $pelajaran['id_pelajaran'],
    'nama_pelajaran' => $pelajaran['nama_pelajaran'],
    'id_guru' => $pelajaran['id_guru'],
    'guru' => $guru_list,
]);
?>
