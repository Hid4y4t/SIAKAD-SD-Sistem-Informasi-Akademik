<?php
require_once '../koneksi/koneksi.php';
require_once 'vendor/vendor/autoload.php'; // Pastikan path ke PhpSpreadsheet benar

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Inisialisasi Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', 'Kelas');
$sheet->setCellValue('C1', 'Jumlah Tagihan');
$sheet->setCellValue('D1', 'Jumlah Terbayar');
$sheet->setCellValue('E1', 'Status');

// Ambil data dari database
$query = "
    SELECT s.nama_siswa, s.kelas, pp.total_tagihan, pp.jumlah_terbayar, pp.status
    FROM ppdb_pembayaran pp
    JOIN siswa s ON pp.id_siswa = s.id_siswa
    ORDER BY s.nama_siswa ASC
";
$result = $mysqli->query($query);

if ($result) {
    $rowNum = 2; // Mulai menulis dari baris kedua

    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A$rowNum", $row['nama_siswa']);
        $sheet->setCellValue("B$rowNum", $row['kelas']);
        $sheet->setCellValue("C$rowNum", 'Rp ' . number_format($row['total_tagihan'], 0, ',', '.'));
        $sheet->setCellValue("D$rowNum", 'Rp ' . number_format($row['jumlah_terbayar'], 0, ',', '.'));
        $sheet->setCellValue("E$rowNum", $row['status']);
        $rowNum++;
    }
} else {
    die("Error: " . $mysqli->error);
}

// Set header agar file diekspor sebagai Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Data_Pembayaran_PPDB.xlsx"');
header('Cache-Control: max-age=0');

// Buat file Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
