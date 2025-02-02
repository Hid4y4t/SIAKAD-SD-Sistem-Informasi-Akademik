<?php
require_once '../koneksi/koneksi.php';
require 'vendor/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan header kolom
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'Tanggal Pembayaran');
$sheet->setCellValue('D1', 'Jumlah Tagihan');
$sheet->setCellValue('E1', 'Status');

// Query untuk mendapatkan data dana_sharing beserta informasi siswa
$query = "
    SELECT ds.*, s.nama_siswa, s.kelas
    FROM dana_sharing ds
    JOIN siswa s ON ds.id_siswa = s.id_siswa
    ORDER BY s.nama_siswa
";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $rowNumber = 2; // Mulai menulis data dari baris kedua
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNumber, $row['nama_siswa']);
        $sheet->setCellValue('B' . $rowNumber, $row['kelas']);
        $sheet->setCellValue('C' . $rowNumber, $row['tanggal_pembayaran'] ? date('d-m-Y', strtotime($row['tanggal_pembayaran'])) : 'Belum ada tanggal');
        $sheet->setCellValue('D' . $rowNumber, $row['jumlah_tagihan']);
        $sheet->setCellValue('E' . $rowNumber, $row['status'] == 1 ? 'Lunas' : 'Belum Lunas');
        $rowNumber++;
    }
}

// Menyimpan file Excel dan mengunduhnya
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Data_Dana_Sharing.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
