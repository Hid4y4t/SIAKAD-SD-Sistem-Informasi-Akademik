<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Ambil `id_siswa` dari URL
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;

// Validasi `id_siswa`
if ($id_siswa <= 0) {
    echo "ID Siswa tidak valid.";
    exit;
}

// Ambil data siswa
$querySiswa = "SELECT nama_siswa, kelas FROM siswa WHERE id_siswa = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("i", $id_siswa);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result()->fetch_assoc();

if (!$resultSiswa) {
    echo "Data siswa tidak ditemukan.";
    exit;
}

// Ambil data riwayat pembayaran dari tabel dana_pengembangan_history berdasarkan id_siswa
$queryHistory = "
    SELECT h.tanggal_bayar, h.jumlah_bayar, h.metode_pembayaran, h.keterangan 
    FROM dana_pengembangan_history h
    JOIN dana_pengembangan dp ON h.id_dana_pengembangan = dp.id_dana_pengembangan
    WHERE dp.id_siswa = ?
    ORDER BY h.tanggal_bayar DESC
";
$stmtHistory = $mysqli->prepare($queryHistory);
$stmtHistory->bind_param("i", $id_siswa);
$stmtHistory->execute();
$resultHistory = $stmtHistory->get_result();

// Set header untuk download file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=riwayat_pengembangan_siswa_{$resultSiswa['nama_siswa']}.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Tampilkan data dalam format tabel HTML untuk Excel
echo "<table border='1'>";
echo "<tr><th colspan='4'>Riwayat Pembayaran Dana Pengembangan</th></tr>";
echo "<tr><td><strong>Nama Siswa</strong></td><td colspan='3'>{$resultSiswa['nama_siswa']}</td></tr>";
echo "<tr><td><strong>Kelas</strong></td><td colspan='3'>{$resultSiswa['kelas']}</td></tr>";
echo "<tr><td colspan='4'></td></tr>";
echo "<tr>
        <th>Tanggal Pembayaran</th>
        <th>Jumlah Bayar</th>
        <th>Metode Pembayaran</th>
        <th>Keterangan</th>
      </tr>";

while ($row = $resultHistory->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . date('d-m-Y', strtotime($row['tanggal_bayar'])) . "</td>";
    echo "<td>Rp " . number_format($row['jumlah_bayar'], 0, ',', '.') . "</td>";
    echo "<td>" . htmlspecialchars($row['metode_pembayaran']) . "</td>";
    echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
    echo "</tr>";
}

if ($resultHistory->num_rows === 0) {
    echo "<tr><td colspan='4'>Tidak ada data riwayat pembayaran.</td></tr>";
}

echo "</table>";

// Tutup statement dan koneksi
$stmtSiswa->close();
$stmtHistory->close();
$mysqli->close();
?>
