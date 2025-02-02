<?php
session_start();
require_once '../koneksi/koneksi.php';
require 'vendor/vendor/autoload.php'; // Pastikan path autoload sesuai dengan struktur project

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data rentang bulan yang dipilih dari form
$bulanDari = isset($_POST['bulan_dari']) ? $_POST['bulan_dari'] : '';
$bulanSampai = isset($_POST['bulan_sampai']) ? $_POST['bulan_sampai'] : '';

// Pastikan bulan dan tahun diisi dengan benar
if (!$bulanDari || !$bulanSampai) {
    echo "Silakan pilih rentang bulan untuk dicetak.";
    exit;
}

// Pecah format bulan untuk mempermudah query
list($tahunDari, $bulanDari) = explode('-', $bulanDari);
list($tahunSampai, $bulanSampai) = explode('-', $bulanSampai);

// Query data pembayaran dalam rentang waktu yang dipilih
$query = "
    SELECT s.nis, s.nama_siswa, s.kelas, p.bulan, p.tahun, p.tanggal_bayar
    FROM pembayaran_spp p
    JOIN siswa s ON p.id_siswa = s.id_siswa
    WHERE (p.tahun > ? OR (p.tahun = ? AND p.bulan >= ?))
      AND (p.tahun < ? OR (p.tahun = ? AND p.bulan <= ?))
    ORDER BY p.tahun, p.bulan
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("iiiiii", $tahunDari, $tahunDari, $bulanDari, $tahunSampai, $tahunSampai, $bulanSampai);
$stmt->execute();
$result = $stmt->get_result();

// Cek jika ada data
if ($result->num_rows === 0) {
    echo "Tidak ada data pembayaran SPP dalam rentang waktu yang dipilih.";
    exit;
}

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Laporan Pembayaran SPP");

// Header kolom
$sheet->setCellValue('A1', 'NIS');
$sheet->setCellValue('B1', 'Nama Siswa');
$sheet->setCellValue('C1', 'Kelas');
$sheet->setCellValue('D1', 'Bulan');
$sheet->setCellValue('E1', 'Tahun');
$sheet->setCellValue('F1', 'Tanggal Bayar');

// Isi data dari hasil query
$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$rowNumber", $row['nis']);
    $sheet->setCellValue("B$rowNumber", $row['nama_siswa']);
    $sheet->setCellValue("C$rowNumber", $row['kelas']);
    $sheet->setCellValue("D$rowNumber", $row['bulan']);
    $sheet->setCellValue("E$rowNumber", $row['tahun']);
    $sheet->setCellValue("F$rowNumber", date('d-m-Y', strtotime($row['tanggal_bayar'])));
    $rowNumber++;
}

// Atur header untuk pengunduhan file Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Laporan_Pembayaran_SPP.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
