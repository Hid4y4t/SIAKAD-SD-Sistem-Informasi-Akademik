<?php
require_once '../koneksi/koneksi.php';
require_once '../tu/vendor/vendor/autoload.php'; // Pastikan PhpSpreadsheet diinstal via Composer
$mysqli->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil parameter
$bulan = $_GET['bulan'] ?? null;
$id_kelas = $_GET['id_kelas'] ?? null;

if (!$bulan || !$id_kelas) {
    die("Parameter tidak valid.");
}

// Ambil nama kelas
$query_kelas = "SELECT nama_kelas FROM kelas WHERE id_kelas = ?";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_kelas);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();
$kelas = $result_kelas->fetch_assoc();
$nama_kelas = $kelas['nama_kelas'];

// Ambil data siswa berdasarkan kelas
$query_siswa = "SELECT id_siswa, nama_siswa FROM siswa WHERE kelas = ?";
$stmt_siswa = $mysqli->prepare($query_siswa);
$stmt_siswa->bind_param("s", $nama_kelas);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();

// Ambil data absensi berdasarkan bulan
$query_absensi = "
    SELECT id_siswa, tanggal, status 
    FROM absensi_siswa 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? AND id_siswa IN (SELECT id_siswa FROM siswa WHERE kelas = ?)
    ORDER BY tanggal ASC";
$stmt_absensi = $mysqli->prepare($query_absensi);
$stmt_absensi->bind_param("ss", $bulan, $nama_kelas);
$stmt_absensi->execute();
$result_absensi = $stmt_absensi->get_result();

// Kelompokkan data absensi berdasarkan siswa dan tanggal
$absensi_data = [];
while ($row = $result_absensi->fetch_assoc()) {
    $absensi_data[$row['id_siswa']][$row['tanggal']] = $row['status'];
}

// Ambil tanggal unik untuk header tabel
$query_tanggal = "
    SELECT DISTINCT tanggal 
    FROM absensi_siswa 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? AND id_siswa IN (SELECT id_siswa FROM siswa WHERE kelas = ?)
    ORDER BY tanggal ASC";
$stmt_tanggal = $mysqli->prepare($query_tanggal);
$stmt_tanggal->bind_param("ss", $bulan, $nama_kelas);
$stmt_tanggal->execute();
$result_tanggal = $stmt_tanggal->get_result();

$tanggal_list = [];
while ($row = $result_tanggal->fetch_assoc()) {
    $tanggal_list[] = $row['tanggal'];
}

// Buat Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$sheet->setCellValue('A1', 'BULAN: ' . date("F Y", strtotime($bulan)));
$sheet->setCellValue('A2', 'KELAS: ' . $nama_kelas);
$sheet->setCellValue('A4', 'NAMA SISWA');
$col = 'B';
foreach ($tanggal_list as $tanggal) {
    $sheet->setCellValue($col . '4', date("d", strtotime($tanggal)));
    $col++;
}

// Data
$rowNum = 5;
while ($siswa = $result_siswa->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNum, $siswa['nama_siswa']);
    $col = 'B';
    foreach ($tanggal_list as $tanggal) {
        $status = $absensi_data[$siswa['id_siswa']][$tanggal] ?? '-';
        $sheet->setCellValue($col . $rowNum, $status === 'Hadir' ? '✔' : ($status === 'Izin' ? 'I' : ($status === 'Alpha' ? 'A' : ($status === 'Sakit' ? 'S' : '-'))));
        $col++;
    }
    $rowNum++;
}

// Simpan file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="absensi_siswa_' . $bulan . '.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
