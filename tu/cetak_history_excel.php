<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';
require 'vendor/vendor/autoload.php'; // Pastikan path ini benar jika menggunakan Composer atau ubah jika letaknya berbeda

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil `id_siswa` dari POST
$id_siswa = isset($_POST['id_siswa']) ? intval($_POST['id_siswa']) : 0;

// Cek validitas `id_siswa`
if ($id_siswa <= 0) {
    echo "<script>alert('ID siswa tidak valid.'); window.close();</script>";
    exit;
}

// Ambil data siswa berdasarkan `id_siswa`
$siswa_query = "SELECT * FROM siswa WHERE id_siswa = ?";
$stmt = $mysqli->prepare($siswa_query);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$siswa_result = $stmt->get_result();

if ($siswa_result->num_rows == 0) {
    echo "<script>alert('Data siswa tidak ditemukan.'); window.close();</script>";
    exit;
}

$siswa_data = $siswa_result->fetch_assoc();
$nama_siswa = $siswa_data['nama_siswa'];
$nis = $siswa_data['nis'];
// Ambil riwayat tabungan siswa berdasarkan `id_siswa`
$history_query = "SELECT tanggal, waktu, pemasukan, pengeluaran, saldo, keterangan FROM tabungan_siswa WHERE id_siswa = ? ORDER BY id_tabungan DESC";
$stmt = $mysqli->prepare($history_query);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$history_result = $stmt->get_result();

if ($history_result->num_rows == 0) {
    echo "<script>alert('Data tabungan untuk siswa ini tidak ditemukan.'); window.close();</script>";
    exit;
}

// Membuat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Judul dan Header
$sheet->setCellValue('A1', "Riwayat Tabungan Siswa: $nama_siswa (NIS: $nis)");
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->getFont()->setBold(true);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('A2', 'Tanggal');
$sheet->setCellValue('B2', 'Waktu');
$sheet->setCellValue('C2', 'Pemasukan');
$sheet->setCellValue('D2', 'Pengeluaran');
$sheet->setCellValue('E2', 'Saldo');
$sheet->setCellValue('F2', 'Keterangan');

// Styling Header
$sheet->getStyle('A2:F2')->getFont()->setBold(true);
$sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center');

// Mengisi data
$rowNumber = 3;
while ($row = $history_result->fetch_assoc()) {
    $sheet->setCellValue("A$rowNumber", $row['tanggal']);
    $sheet->setCellValue("B$rowNumber", $row['waktu']);
    $sheet->setCellValue("C$rowNumber", "Rp " . number_format($row['pemasukan'], 2, ',', '.'));
    $sheet->setCellValue("D$rowNumber", "Rp " . number_format($row['pengeluaran'], 2, ',', '.'));
    $sheet->setCellValue("E$rowNumber", "Rp " . number_format($row['saldo'], 2, ',', '.'));
    $sheet->setCellValue("F$rowNumber", $row['keterangan']);
    $rowNumber++;
}

// Auto-size columns for readability
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Menulis file Excel
$filename = "history_tabungan_siswa_{$id_siswa}_{$nama_siswa}.xlsx";
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=$filename");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
