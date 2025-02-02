<?php
require 'vendor/vendor/autoload.php'; // pastikan PhpSpreadsheet ter-load
require '../koneksi/koneksi.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



$type = $_GET['type'] ?? 'selected';
$selected_ids = isset($_POST['selected_ids']) ? json_decode($_POST['selected_ids']) : [];

$query = "SELECT s.nama_siswa, s.kelas, jb.nama_beasiswa, b.jumlah 
          FROM beasiswa b
          JOIN siswa s ON b.id_siswa = s.id_siswa
          JOIN jenis_beasiswa jb ON b.jenis_beasiswa = jb.id_beasiswa_js";

if ($type === 'selected' && !empty($selected_ids)) {
    $ids = implode(",", array_map('intval', $selected_ids));
    $query .= " WHERE b.id_beasiswa IN ($ids)";
}

$result = $mysqli->query($query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Nama');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'Jenis Beasiswa');
$sheet->setCellValue('D1', 'Nominal');

$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['nama_siswa']);
    $sheet->setCellValue('B' . $rowNumber, $row['kelas']);
    $sheet->setCellValue('C' . $rowNumber, $row['nama_beasiswa']);
    $sheet->setCellValue('D' . $rowNumber, $row['jumlah']);
    $rowNumber++;
}

$filename = $type === 'all' ? 'Beasiswa_Semua.xlsx' : 'Beasiswa_Pilihan.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
