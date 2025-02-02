<?php
require_once '../koneksi/koneksi.php';

$tanggal = $_GET['tanggal'] ?? null;
$kode_kelas = $_GET['kode_kelas'] ?? null;

if (!$tanggal || !$kode_kelas) {
    echo json_encode(['error' => 'Parameter tidak lengkap.']);
    exit;
}

// Periksa apakah absensi sudah dilakukan
$query = "
    SELECT COUNT(*) AS absensiAda 
    FROM absensi_siswa 
    WHERE tanggal = ? 
    AND id_siswa IN (SELECT id_siswa FROM siswa WHERE kelas = (SELECT nama_kelas FROM kelas WHERE kode_kelas = ?))
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ss", $tanggal, $kode_kelas);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode(['absensiAda' => $result['absensiAda'] > 0]);
?>
