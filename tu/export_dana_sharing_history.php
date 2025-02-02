<?php
require_once '../koneksi/koneksi.php';
require 'vendor/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Pastikan id_siswa tersedia di URL
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;
if ($id_siswa <= 0) {
    die("ID Siswa tidak valid.");
}

// Ambil data siswa untuk menampilkan nama pada file Excel
$querySiswa = "SELECT nama_siswa FROM siswa WHERE id_siswa = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("i", $id_siswa);
$stmtSiswa->execute();
$siswaData = $stmtSiswa->get_result()->fetch_assoc();
$stmtSiswa->close();

if (!$siswaData) {
    die("Data siswa tidak ditemukan.");
}

// Ambil data history pembayaran sesuai dengan id_siswa
$queryHistory = "SELECT tanggal_bayar, jumlah_bayar, metode_pembayaran, keterangan FROM dana_sharing_history WHERE id_siswa = ? ORDER BY tanggal_bayar DESC";
$stmtHistory = $mysqli->prepare($queryHistory);
$stmtHistory->bind_param("i", $id_siswa);
$stmtHistory->execute();
$resultHistory = $stmtHistory->get_result();

// Inisialisasi Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan header kolom
$sheet->setCellValue('A1', 'Nama Siswa');
$sheet->setCellValue('B1', $siswaData['nama_siswa']);
$sheet->setCellValue('A3', 'Tanggal Pembayaran');
$sheet->setCellValue('B3', 'Jumlah Bayar');
$sheet->setCellValue('C3', 'Metode Pembayaran');
$sheet->setCellValue('D3', 'Keterangan');

// Mengisi data dari tabel ke file Excel
$rowNumber = 4; // Mulai dari baris keempat setelah header
while ($row = $resultHistory->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, date('d-m-Y', strtotime($row['tanggal_bayar'])));
    $sheet->setCellValue('B' . $rowNumber, $row['jumlah_bayar']);
    $sheet->setCellValue('C' . $rowNumber, $row['metode_pembayaran']);
    $sheet->setCellValue('D' . $rowNumber, $row['keterangan'] ?? '-');
    $rowNumber++;
}

// Atur header untuk unduhan file Excel
$fileName = 'History_Pembayaran_Dana_Sharing_' . $siswaData['nama_siswa'] . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");
header('Cache-Control: max-age=0');

// Simpan dan kirimkan file Excel ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
?>
