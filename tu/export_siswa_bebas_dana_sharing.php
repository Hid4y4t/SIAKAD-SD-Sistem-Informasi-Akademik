<?php
require_once '../koneksi/koneksi.php';
require_once 'vendor/vendor/autoload.php'; // Pastikan path ini sesuai dengan lokasi PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Query untuk mendapatkan data siswa bebas dana sharing
$query = "
    SELECT s.nama_siswa, s.kelas, bds.alasan_bebas, bds.tanggal_mulai, bds.tanggal_selesai 
    FROM siswa_bebas_dana_sharing bds 
    JOIN siswa s ON bds.id_siswa = s.id_siswa
";
$result = $mysqli->query($query);

if (!$result) {
    die("Query Error: " . $mysqli->error);
}

// Membuat file spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan header kolom
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'Alasan Bebas');
$sheet->setCellValue('D1', 'Tanggal Mulai');
$sheet->setCellValue('E1', 'Tanggal Selesai');

// Mengisi data dari database ke file Excel
$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['nama_siswa']);
    $sheet->setCellValue('B' . $rowNumber, $row['kelas']);
    $sheet->setCellValue('C' . $rowNumber, $row['alasan_bebas']);
    $sheet->setCellValue('D' . $rowNumber, date('d-m-Y', strtotime($row['tanggal_mulai'])));
    $sheet->setCellValue('E' . $rowNumber, $row['tanggal_selesai'] ? date('d-m-Y', strtotime($row['tanggal_selesai'])) : 'Tidak ada');
    $rowNumber++;
}

// Mengatur nama file dan header untuk download
$fileName = "Laporan_Siswa_Bebas_Dana_Sharing.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");
header('Cache-Control: max-age=0');

// Menyimpan file Excel dan mengunduhnya
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
