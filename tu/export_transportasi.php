<?php
require_once '../koneksi/koneksi.php';
require_once 'vendor/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data bulan dan tahun dari form
$startMonth = isset($_GET['startMonth']) ? $_GET['startMonth'] : null;
$startYear = isset($_GET['startYear']) ? $_GET['startYear'] : null;
$endMonth = isset($_GET['endMonth']) ? $_GET['endMonth'] : null;
$endYear = isset($_GET['endYear']) ? $_GET['endYear'] : null;

if (!$startMonth || !$startYear || !$endMonth || !$endYear) {
    die("Pilih rentang bulan dan tahun yang valid.");
}

// Format tanggal mulai dan akhir untuk digunakan dalam query
$startDate = "{$startYear}-{$startMonth}-01";
$endDate = date("Y-m-t", strtotime("{$endYear}-{$endMonth}-01"));

// Query data pembayaran transportasi sesuai rentang bulan dan tahun
$query = "
    SELECT s.nama_siswa, s.kelas, th.tanggal_bayar, th.jumlah_bayar, th.metode_pembayaran, z.nama_zona
    FROM transportasi_history th
    JOIN transportasi_pembayaran tp ON th.id_pembayaran = tp.id_pembayaran
    JOIN siswa s ON tp.id_siswa = s.id_siswa
    JOIN zona_transportasi z ON tp.id_zona = z.id_zona
    WHERE th.tanggal_bayar BETWEEN ? AND ?
    ORDER BY th.tanggal_bayar ASC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

// Buat Spreadsheet baru untuk Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul kolom
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'Zona');
$sheet->setCellValue('D1', 'Tanggal Bayar');
$sheet->setCellValue('E1', 'Jumlah Bayar');
$sheet->setCellValue('F1', 'Metode Pembayaran');

// Isi data
$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['nama_siswa']);
    $sheet->setCellValue('B' . $rowNumber, $row['kelas']);
    $sheet->setCellValue('C' . $rowNumber, $row['nama_zona']);
    $sheet->setCellValue('D' . $rowNumber, date('d-m-Y', strtotime($row['tanggal_bayar'])));
    $sheet->setCellValue('E' . $rowNumber, $row['jumlah_bayar']);
    $sheet->setCellValue('F' . $rowNumber, $row['metode_pembayaran']);
    $rowNumber++;
}

// Nama file berdasarkan rentang waktu
$fileName = "Data_Transportasi_{$startMonth}_{$startYear}_to_{$endMonth}_{$endYear}.xlsx";

// Simpan dan download file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
