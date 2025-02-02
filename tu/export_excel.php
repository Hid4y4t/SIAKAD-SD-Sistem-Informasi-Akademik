<?php
require 'vendor/vendor/autoload.php'; // pastikan PhpSpreadsheet ter-load
require '../koneksi/koneksi.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$type = $_GET['type'] ?? 'selected';
$selected_ids = isset($_POST['selected_ids']) ? json_decode($_POST['selected_ids']) : [];

$query = "SELECT s.nama_siswa, s.kelas, jp.nama_potongan, p.jumlah 
          FROM potongan_spp p
          JOIN siswa s ON p.id_siswa = s.id_siswa
          JOIN jenis_potongan_spp jp ON p.jenis_potongan = jp.id_potongan";

if ($type === 'selected' && !empty($selected_ids)) {
    $ids = implode(",", array_map('intval', $selected_ids));
    $query .= " WHERE p.id_potongan IN ($ids)";
}

$result = $mysqli->query($query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Nama');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'Jenis Potongan');
$sheet->setCellValue('D1', 'Nominal');

$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['nama_siswa']);
    $sheet->setCellValue('B' . $rowNumber, $row['kelas']);
    $sheet->setCellValue('C' . $rowNumber, $row['nama_potongan']);
    $sheet->setCellValue('D' . $rowNumber, $row['jumlah']);
    $rowNumber++;
}

$filename = $type === 'all' ? 'Potongan_SPP_Semua.xlsx' : 'Potongan_SPP_Pilihan.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
