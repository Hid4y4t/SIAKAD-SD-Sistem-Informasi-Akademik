<?php
require_once '../koneksi/koneksi.php';

header('Content-Type: application/json');

session_start();

// Periksa apakah pengguna sudah login dan merupakan wali kelas
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    echo json_encode(['error' => 'Anda tidak memiliki akses ke data ini.']);
    exit;
}

$id_guru = $_SESSION['id_guru'];

// Periksa apakah parameter id_siswa telah dikirim
if (!isset($_GET['id_siswa']) || empty($_GET['id_siswa'])) {
    echo json_encode(['error' => 'ID siswa tidak ditemukan.']);
    exit;
}

$id_siswa = intval($_GET['id_siswa']); // Pastikan id_siswa adalah integer

// Query untuk memastikan bahwa siswa tersebut termasuk dalam kelas yang diampu oleh wali kelas
$query_validasi = "
    SELECT s.id_siswa
    FROM siswa s
    JOIN kelas k ON s.kelas = k.nama_kelas
    WHERE s.id_siswa = ? AND k.wali_kelas = ?
";
$stmt_validasi = $mysqli->prepare($query_validasi);
$stmt_validasi->bind_param("ii", $id_siswa, $id_guru);
$stmt_validasi->execute();
$result_validasi = $stmt_validasi->get_result();

if ($result_validasi->num_rows === 0) {
    echo json_encode(['error' => 'Anda tidak memiliki akses ke data siswa ini.']);
    exit;
}

// Query untuk mengambil data catatan siswa
$query_catatan = "
    SELECT tanggal, catatan_guru
    FROM catatan_siswa
    WHERE id_siswa = ?
    ORDER BY tanggal ASC
";
$stmt_catatan = $mysqli->prepare($query_catatan);
$stmt_catatan->bind_param("i", $id_siswa);
$stmt_catatan->execute();
$result_catatan = $stmt_catatan->get_result();

$catatan = [];
while ($row = $result_catatan->fetch_assoc()) {
    $catatan[] = $row; // Masukkan setiap catatan ke array
}

// Kirimkan data dalam format JSON
echo json_encode($catatan);
exit;
?>
