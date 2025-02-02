<?php
require_once '../koneksi/koneksi.php';

// Pastikan ada `id_bebas` yang dikirim dari permintaan AJAX
if (!isset($_GET['id_bebas'])) {
    echo json_encode(['error' => 'ID tidak ditemukan.']);
    exit;
}

$id_bebas = intval($_GET['id_bebas']);

// Ambil data detail berdasarkan `id_bebas`
$query = "
    SELECT bdp.*, s.nama_siswa, s.kelas
    FROM siswa_bebas_ppdb bdp
    JOIN siswa s ON bdp.id_siswa = s.id_siswa
    WHERE bdp.id_bebas = ?
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_bebas);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Data tidak ditemukan.']);
    exit;
}

$data = $result->fetch_assoc();

// Kembalikan data sebagai JSON
echo json_encode($data);
