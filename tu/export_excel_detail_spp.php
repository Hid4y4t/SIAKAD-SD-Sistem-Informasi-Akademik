<?php
require_once '../koneksi/koneksi.php';
require_once 'vendor/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$nis = isset($_GET['nis']) ? $_GET['nis'] : '';

// Ambil data siswa berdasarkan NIS
$querySiswa = "
    SELECT s.nis, s.nama_siswa, s.kelas
    FROM siswa s
    WHERE s.nis = ?
";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("s", $nis);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();
$siswa = $resultSiswa->fetch_assoc();

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

// Ambil riwayat pembayaran siswa dari tabel `nota`
$queryDetailPembayaran = "
    SELECT n.jenis_pembayaran, n.jenis_potongan, n.jumlah_dibayarkan, n.keterangan, n.tanggal_pembayaran
    FROM nota n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    WHERE s.nis = ?
    ORDER BY n.tanggal_pembayaran DESC
";
$stmt = $mysqli->prepare($queryDetailPembayaran);
$stmt->bind_param("s", $nis);
$stmt->execute();
$resultDetailPembayaran = $stmt->get_result();

if (!$resultDetailPembayaran) {
    die("Query Error: " . $mysqli->error);
}

// Membuat file Excel baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan judul
$sheet->setCellValue('A1', 'Detail Riwayat Pembayaran SPP');
$sheet->setCellValue('A3', 'NIS: ' . $siswa['nis']);
$sheet->setCellValue('B3', 'Nama: ' . $siswa['nama_siswa']);
$sheet->setCellValue('C3', 'Kelas: ' . $siswa['kelas']);

// Menambahkan header kolom
$sheet->setCellValue('A5', 'Jenis Pembayaran');
$sheet->setCellValue('B5', 'Jenis Potongan');
$sheet->setCellValue('C5', 'Jumlah Dibayarkan');
$sheet->setCellValue('D5', 'Keterangan');
$sheet->setCellValue('E5', 'Tanggal Pembayaran');

// Mengisi data riwayat pembayaran
$rowNumber = 6; // Mulai dari baris ke-6
while ($row = $resultDetailPembayaran->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['jenis_pembayaran'] ?? '');
    $sheet->setCellValue('B' . $rowNumber, $row['jenis_potongan'] ?? '');
    $sheet->setCellValue('C' . $rowNumber, 'Rp ' . number_format($row['jumlah_dibayarkan'] ?? 0, 0, ',', '.'));
    $sheet->setCellValue('D' . $rowNumber, $row['keterangan'] ?? '');
    $sheet->setCellValue('E' . $rowNumber, date('d-m-Y', strtotime($row['tanggal_pembayaran'] ?? '')));
    $rowNumber++;
}

// Menyiapkan nama file dan header untuk download
$fileName = "Riwayat_Pembayaran_SPP_" . $siswa['nama_siswa'] . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");
header('Cache-Control: max-age=0');

// Menyimpan file Excel ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
