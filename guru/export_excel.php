<?php
require_once '../koneksi/koneksi.php';

// Ambil parameter dari URL
$id_kelas = $_GET['id_kelas'];
$id_guru = $_GET['id_guru'];
$mapel = $_GET['mapel'];

// Validasi parameter
if (empty($id_kelas) || empty($id_guru) || empty($mapel)) {
    die("Parameter tidak valid.");
}

// Ambil data nilai
$query = "
    SELECT 
        s.nama_siswa, 
        n.nilai, 
        n.catatan, 
        n.tanggal 
    FROM 
        nilai_ulangan_harian n
    JOIN 
        siswa s ON n.id_siswa = s.id_siswa
    WHERE 
        n.id_kelas = ? AND n.id_guru = ? AND n.mapel = ?
    ORDER BY n.tanggal DESC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iis", $id_kelas, $id_guru, $mapel);
$stmt->execute();
$result = $stmt->get_result();

// Header untuk Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=nilai_ulangan_harian.xls");

// Cetak data
echo "<table border='1'>";
echo "<tr><th>Nama Siswa</th><th>Nilai</th><th>Catatan</th><th>Tanggal</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['nama_siswa']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nilai']) . "</td>";
    echo "<td>" . htmlspecialchars($row['catatan']) . "</td>";
    echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
