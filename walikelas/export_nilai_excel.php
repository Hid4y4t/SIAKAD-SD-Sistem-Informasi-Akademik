<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna login
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil parameter dari URL
$id_kelas = $_GET['id_kelas'] ?? null;
$nama_pelajaran = $_GET['nama_pelajaran'] ?? null;

// Validasi parameter
if (!$id_kelas || !$nama_pelajaran) {
    die("Parameter tidak valid.");
}

// Ambil data siswa berdasarkan kelas
$query_siswa = "SELECT id_siswa, nama_siswa FROM siswa WHERE kelas = (SELECT nama_kelas FROM kelas WHERE id_kelas = ?)";
$stmt_siswa = $mysqli->prepare($query_siswa);
$stmt_siswa->bind_param("i", $id_kelas);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();

// Ambil data nilai berdasarkan mata pelajaran dan kelas
$query_nilai = "SELECT id_siswa, tanggal, nilai FROM nilai_ulangan_harian WHERE id_kelas = ? AND mapel = ? ORDER BY tanggal ASC";
$stmt_nilai = $mysqli->prepare($query_nilai);
$stmt_nilai->bind_param("is", $id_kelas, $nama_pelajaran);
$stmt_nilai->execute();
$result_nilai = $stmt_nilai->get_result();

$nilai_data = [];
while ($row = $result_nilai->fetch_assoc()) {
    $nilai_data[$row['id_siswa']][$row['tanggal']] = $row['nilai'];
}

// Ambil tanggal-tanggal unik
$tanggal_query = "SELECT DISTINCT tanggal FROM nilai_ulangan_harian WHERE id_kelas = ? AND mapel = ? ORDER BY tanggal ASC";
$stmt_tanggal = $mysqli->prepare($tanggal_query);
$stmt_tanggal->bind_param("is", $id_kelas, $nama_pelajaran);
$stmt_tanggal->execute();
$result_tanggal = $stmt_tanggal->get_result();

$tanggal_list = [];
while ($row = $result_tanggal->fetch_assoc()) {
    $tanggal_list[] = $row['tanggal'];
}

// Header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=nilai_ulangan_" . urlencode($nama_pelajaran) . ".xls");
header("Cache-Control: max-age=0");

// Membuat tabel Excel
echo "<table border='1'>";
echo "<tr>";
echo "<th>Nama Siswa</th>";
foreach ($tanggal_list as $tanggal) {
    echo "<th>" . date("d-m-Y", strtotime($tanggal)) . "</th>";
}
echo "</tr>";

while ($siswa = $result_siswa->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($siswa['nama_siswa']) . "</td>";
    foreach ($tanggal_list as $tanggal) {
        echo "<td>" . ($nilai_data[$siswa['id_siswa']][$tanggal] ?? '-') . "</td>";
    }
    echo "</tr>";
}

echo "</table>";
?>
