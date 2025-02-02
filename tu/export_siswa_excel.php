<?php
require '../koneksi/koneksi.php';
require 'vendor/vendor/autoload.php'; // Autoload PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil parameter ids dari URL
$ids = isset($_GET['ids']) ? explode(",", $_GET['ids']) : [];

if (empty($ids)) {
    echo "Tidak ada data yang dipilih untuk diekspor.";
    exit;
}

// Query untuk mengambil data siswa berdasarkan NIS yang dipilih
$idPlaceholders = implode(",", array_fill(0, count($ids), "?"));
$query = "SELECT * FROM siswa WHERE nis IN ($idPlaceholders)";
$stmt = $mysqli->prepare($query);
$stmt->bind_param(str_repeat("s", count($ids)), ...$ids);
$stmt->execute();
$result = $stmt->get_result();

// Buat file Excel baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header untuk kolom
$sheet->setCellValue('A1', 'NIS');
$sheet->setCellValue('B1', 'Nama');
$sheet->setCellValue('C1', 'Kelas');
$sheet->setCellValue('D1', 'Tanggal Lahir');
$sheet->setCellValue('E1', 'Tahun Angkatan');
$sheet->setCellValue('F1', 'Jenis Kelamin');
$sheet->setCellValue('G1', 'Status');

// Isi data siswa ke dalam Excel
$row = 2;
while ($data = $result->fetch_assoc()) {
    $sheet->setCellValue("A{$row}", $data['nis']);
    $sheet->setCellValue("B{$row}", $data['nama_siswa']);
    $sheet->setCellValue("C{$row}", $data['kelas']);
    $sheet->setCellValue("D{$row}", $data['tanggal_lahir']);
    $sheet->setCellValue("E{$row}", $data['angkatan']);
    $sheet->setCellValue("F{$row}", $data['jenis_kelamin']);
    $sheet->setCellValue("G{$row}", $data['status'] == 1 ? 'Aktif' : ($data['status'] == 0 ? 'Off' : 'Pindah'));
    $row++;
}

// Set nama file dan header untuk unduhan
$filename = "data_siswa_terpilih.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");

// Buat dan unduh file Excel
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>
