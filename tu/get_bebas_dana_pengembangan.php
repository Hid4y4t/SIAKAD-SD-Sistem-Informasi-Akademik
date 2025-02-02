<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// Pastikan id_bebas ada dalam parameter GET
if (!isset($_GET['id_bebas'])) {
    echo json_encode(['error' => 'ID bebas tidak ditemukan.']);
    exit;
}

$id_bebas = intval($_GET['id_bebas']);

// Query untuk mengambil data berdasarkan id_bebas
$query = "
    SELECT sb.id_bebas, s.nama_siswa, s.kelas, sb.alasan_bebas, sb.tanggal_mulai, sb.tanggal_selesai
    FROM siswa_bebas_dana_pengembangan sb
    JOIN siswa s ON sb.id_siswa = s.id_siswa
    WHERE sb.id_bebas = ?
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
echo json_encode($data);

// Tutup koneksi
$stmt->close();
$mysqli->close();
?>
