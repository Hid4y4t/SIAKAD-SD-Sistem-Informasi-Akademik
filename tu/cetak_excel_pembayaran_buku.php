<?php
require '../koneksi/koneksi.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Query data pembayaran buku
$query = "
    SELECT s.nama_siswa, s.kelas, b.jenis_buku, bp.total_tagihan, bp.jumlah_terbayar, bp.status 
    FROM buku_pembayaran bp
    JOIN siswa s ON bp.id_siswa = s.id_siswa
    JOIN buku b ON bp.id_buku = b.id_buku
";
$result = $mysqli->query($query);

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Kolom
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'Jenis Buku');
$sheet->setCellValue('D1', 'Total Tagihan');
$sheet->setCellValue('E1', 'Jumlah Terbayar');
$sheet->setCellValue('F1', 'Status');

// Isi Data
$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowIndex, $row['nama_siswa']);
    $sheet->setCellValue('B' . $rowIndex, $row['kelas']);
    $sheet->setCellValue('C' . $rowIndex, $row['jenis_buku']);
    $sheet->setCellValue('D' . $rowIndex, $row['total_tagihan']);
    $sheet->setCellValue('E' . $rowIndex, $row['jumlah_terbayar']);
    $sheet->setCellValue('F' . $rowIndex, $row['status']);
    $rowIndex++;
}

// Buat file Excel
$writer = new Xlsx($spreadsheet);
$filename = 'data_pembayaran_buku.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
