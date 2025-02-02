<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Ambil data dari tabel `dana_pengembangan` dan informasi terkait siswa
$query = "
    SELECT dp.id_dana_pengembangan, s.nama_siswa, s.kelas, s.nis, d.jumlah_total AS total_tagihan, dp.jumlah_terbayar, dp.status
    FROM dana_pengembangan dp
    JOIN siswa s ON dp.id_siswa = s.id_siswa
    JOIN dana_pengembangan_nominal d ON dp.id_nominal = d.id_nominal
";
$result = $mysqli->query($query);

if (!$result) {
    die("Query Error: " . $mysqli->error);
}

// Set header untuk mengunduh file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_dana_pengembangan.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Tampilkan data dalam format tabel HTML untuk Excel
echo "<table border='1'>";
echo "<tr><th colspan='6'>Data Pembayaran Dana Pengembangan</th></tr>";
echo "<tr>
        <th>NIS</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Total Tagihan</th>
        <th>Jumlah Terbayar</th>
        <th>Status</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['nis']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_siswa']) . "</td>";
    echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
    echo "<td>Rp " . number_format($row['total_tagihan'], 0, ',', '.') . "</td>";
    echo "<td>Rp " . number_format($row['jumlah_terbayar'], 0, ',', '.') . "</td>";
    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
    echo "</tr>";
}

if ($result->num_rows === 0) {
    echo "<tr><td colspan='6'>Tidak ada data dana pengembangan.</td></tr>";
}

echo "</table>";

// Tutup koneksi
$mysqli->close();
?>
