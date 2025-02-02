<?php
require '../koneksi/koneksi.php';
require 'vendor/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil id_siswa dari parameter URL
$id_siswa = $_GET['id_siswa'];

// Query untuk mengambil data siswa
$querySiswa = "SELECT nama_siswa, kelas FROM siswa WHERE id_siswa = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("i", $id_siswa);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();
$siswa = $resultSiswa->fetch_assoc();

// Jika siswa tidak ditemukan, hentikan proses
if (!$siswa) {
    echo "Siswa tidak ditemukan.";
    exit;
}

// Query untuk mengambil riwayat pembayaran buku berdasarkan id_siswa
$queryHistory = "
    SELECT bh.*, b.jenis_buku, bp.total_tagihan, bp.jumlah_terbayar
    FROM buku_history bh
    JOIN buku_pembayaran bp ON bh.id_pembayaran = bp.id_pembayaran
    JOIN buku b ON bp.id_buku = b.id_buku
    WHERE bp.id_siswa = ?
    ORDER BY bh.tanggal_bayar DESC
";
$stmtHistory = $mysqli->prepare($queryHistory);
$stmtHistory->bind_param("i", $id_siswa);
$stmtHistory->execute();
$resultHistory = $stmtHistory->get_result();

// Membuat file spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Mengisi header pada file Excel
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', $siswa['nama_siswa']);
$sheet->setCellValue('A2', 'Kelas');
$sheet->setCellValue('B2', $siswa['kelas']);

// Header kolom data riwayat pembayaran
$sheet->setCellValue('A4', 'Tanggal Bayar');
$sheet->setCellValue('B4', 'Jenis Buku');
$sheet->setCellValue('C4', 'Total Tagihan');
$sheet->setCellValue('D4', 'Jumlah Terbayar');
$sheet->setCellValue('E4', 'Jumlah Bayar (Cicilan)');
$sheet->setCellValue('F4', 'Metode Pembayaran');
$sheet->setCellValue('G4', 'Keterangan');

// Mengisi data riwayat pembayaran ke dalam file Excel
$rowIndex = 5;
while ($row = $resultHistory->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowIndex, date('d-m-Y', strtotime($row['tanggal_bayar'])));
    $sheet->setCellValue('B' . $rowIndex, $row['jenis_buku']);
    $sheet->setCellValue('C' . $rowIndex, $row['total_tagihan']);
    $sheet->setCellValue('D' . $rowIndex, $row['jumlah_terbayar']);
    $sheet->setCellValue('E' . $rowIndex, $row['jumlah_bayar']);
    $sheet->setCellValue('F' . $rowIndex, $row['metode_pembayaran']);
    $sheet->setCellValue('G' . $rowIndex, $row['keterangan']);
    $rowIndex++;
}

// Buat file Excel
$writer = new Xlsx($spreadsheet);
$filename = 'riwayat_pembayaran_buku_' . $siswa['nama_siswa'] . '.xlsx';

// Header untuk download file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
