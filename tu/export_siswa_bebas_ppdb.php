<?php
require_once '../koneksi/koneksi.php';
require_once 'vendor/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Query untuk mengambil data siswa yang bebas PPDB
$query = "
    SELECT s.nama_siswa, s.kelas, sb.alasan_bebas, sb.tanggal_mulai, sb.tanggal_selesai
    FROM siswa_bebas_ppdb sb
    JOIN siswa s ON sb.id_siswa = s.id_siswa
    ORDER BY sb.tanggal_mulai DESC
";
$result = $mysqli->query($query);

if (!$result) {
    die("Error dalam mengambil data: " . $mysqli->error);
}

// Membuat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan header
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'Alasan Bebas');
$sheet->setCellValue('D1', 'Tanggal Mulai');
$sheet->setCellValue('E1', 'Tanggal Selesai');

// Mengisi data dari database ke spreadsheet
$rowNumber = 2; // Mulai dari baris kedua untuk data
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['nama_siswa']);
    $sheet->setCellValue('B' . $rowNumber, $row['kelas']);
    $sheet->setCellValue('C' . $rowNumber, $row['alasan_bebas']);
    $sheet->setCellValue('D' . $rowNumber, date('d-m-Y', strtotime($row['tanggal_mulai'])));
    $sheet->setCellValue('E' . $rowNumber, $row['tanggal_selesai'] ? date('d-m-Y', strtotime($row['tanggal_selesai'])) : 'Tidak ada');
    $rowNumber++;
}

// Set header HTTP untuk pengunduhan file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Siswa_Bebas_PPDB.xlsx"');
header('Cache-Control: max-age=0');

// Buat writer untuk menulis file ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
