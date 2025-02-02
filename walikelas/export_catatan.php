<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=catatan_wali_kelas.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil ID wali kelas dari session
$id_guru = $_SESSION['id_guru'];

// Ambil data kelas yang menjadi tanggung jawab wali kelas ini
$query_kelas = "SELECT nama_kelas, id_kelas FROM kelas WHERE wali_kelas = ?";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_guru);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();

if ($result_kelas->num_rows === 0) {
    echo "Tidak ada data kelas untuk wali kelas ini.";
    exit;
}

while ($kelas = $result_kelas->fetch_assoc()) {
    $id_kelas = $kelas['id_kelas'];

    // Ambil data catatan siswa
    $query_catatan = "
        SELECT s.nama_siswa, c.tanggal, c.permasalahan, c.solusi, c.catatan_tambahan, c.dilaporkan_kepada
        FROM catatan_wali_kelas c
        JOIN siswa s ON c.id_siswa = s.id_siswa
        WHERE c.id_kelas = ?
        ORDER BY c.tanggal DESC
    ";
    $stmt_catatan = $mysqli->prepare($query_catatan);
    $stmt_catatan->bind_param("i", $id_kelas);
    $stmt_catatan->execute();
    $result_catatan = $stmt_catatan->get_result();

    echo "<table border='1'>";
    echo "<tr>
            <th colspan='5'>Kelas: {$kelas['nama_kelas']}</th>
          </tr>";
    echo "<tr>
            <th>Nama Siswa</th>
            <th>Tanggal</th>
            <th>Permasalahan</th>
            <th>Solusi</th>
            <th>Catatan Tambahan</th>
            <th>Dilaporkan Kepada</th>
          </tr>";

    if ($result_catatan->num_rows > 0) {
        while ($catatan = $result_catatan->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($catatan['nama_siswa']) . "</td>
                    <td>" . htmlspecialchars($catatan['tanggal']) . "</td>
                    <td>" . htmlspecialchars($catatan['permasalahan']) . "</td>
                    <td>" . htmlspecialchars($catatan['solusi']) . "</td>
                    <td>" . htmlspecialchars($catatan['catatan_tambahan']) . "</td>
                    <td>" . htmlspecialchars($catatan['dilaporkan_kepada']) . "</td>
                  </tr>";
        }
    } else {
        echo "<tr>
                <td colspan='6'>Tidak ada data catatan untuk kelas ini.</td>
              </tr>";
    }
    echo "</table><br>";
}
?>
