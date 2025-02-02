<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;
if ($id_siswa <= 0) {
    die("ID Siswa tidak valid.");
}

$siswaQuery = "SELECT nama_siswa, kelas, nis FROM siswa WHERE id_siswa = ?";
$stmt = $mysqli->prepare($siswaQuery);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$siswaData = $stmt->get_result()->fetch_assoc();

if (!$siswaData) {
    die("Siswa tidak ditemukan.");
}

// Ambil data riwayat pembayaran untuk siswa
$queryHistory = "
    SELECT ph.tanggal_bayar, ph.jumlah_bayar, ph.metode_pembayaran, ph.keterangan
    FROM ppdb_history ph
    JOIN ppdb_pembayaran pp ON ph.id_pembayaran = pp.id_pembayaran
    WHERE pp.id_siswa = ?
    ORDER BY ph.tanggal_bayar DESC
";
$stmtHistory = $mysqli->prepare($queryHistory);
$stmtHistory->bind_param("i", $id_siswa);
$stmtHistory->execute();
$resultHistory = $stmtHistory->get_result();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Riwayat_Pembayaran_PPDB_" . $siswaData['nama_siswa'] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr><th colspan='4'>Riwayat Pembayaran PPDB</th></tr>";
echo "<tr><td><strong>Nama Siswa:</strong></td><td>{$siswaData['nama_siswa']}</td><td><strong>Kelas:</strong></td><td>{$siswaData['kelas']}</td></tr>";
echo "<tr><td><strong>NIS:</strong></td><td>{$siswaData['nis']}</td><td></td><td></td></tr>";
echo "<tr><td colspan='4'></td></tr>";

echo "<tr><th>Tanggal Bayar</th><th>Jumlah Bayar</th><th>Metode Pembayaran</th><th>Keterangan</th></tr>";

while ($row = $resultHistory->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . date('d-m-Y', strtotime($row['tanggal_bayar'])) . "</td>";
    echo "<td>Rp " . number_format($row['jumlah_bayar'], 0, ',', '.') . "</td>";
    echo "<td>{$row['metode_pembayaran']}</td>";
    echo "<td>{$row['keterangan']}</td>";
    echo "</tr>";
}
echo "</table>";
?>
