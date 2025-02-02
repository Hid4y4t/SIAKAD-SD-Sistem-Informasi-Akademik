<?php
require_once '../koneksi/koneksi.php';
require_once '../tu/vendor/vendor/autoload.php';
$mysqli->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['bulan']) || !isset($_GET['id_kelas'])) {
    die("Parameter tidak valid.");
}

$bulan = $_GET['bulan'];
$id_kelas = $_GET['id_kelas'];

// Ambil data jurnal kelas berdasarkan bulan dan id_kelas
$query = "
    SELECT 
        nama_mapel, 
        jam_pelajaran, 
        topik_pembahasan, 
        catatan, 
        tanggal
    FROM 
        jurnal_kelas 
    WHERE 
        DATE_FORMAT(tanggal, '%Y-%m') = ? AND id_kelas = ?
    ORDER BY tanggal ASC, jam_pelajaran ASC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $bulan, $id_kelas);
$stmt->execute();
$result = $stmt->get_result();

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Atur judul file Excel
$sheet->setTitle("Jurnal Kelas");

// Header utama
$row = 1;
$sheet->setCellValue('A' . $row, 'Jurnal Kelas');
$row++;
$sheet->setCellValue('A' . $row, 'Bulan: ' . date("F Y", strtotime($bulan)));
$row++;
$sheet->setCellValue('A' . $row, 'Kelas: ' . $id_kelas);
$row++;
$row++; // Tambahkan spasi untuk header tabel

// Data berdasarkan tanggal
$current_date = null;
while ($data = $result->fetch_assoc()) {
    // Jika tanggal berubah, buat header baru
    if ($current_date !== $data['tanggal']) {
        if ($current_date !== null) {
            $row++; // Tambahkan spasi antara tabel tanggal sebelumnya dan tabel berikutnya
        }
        $current_date = $data['tanggal'];

        // Header tanggal
        $sheet->setCellValue('A' . $row, 'Bulan: ' . date("F Y", strtotime($current_date)));
        $sheet->setCellValue('B' . $row, 'Tanggal: ' . $current_date);
        $sheet->setCellValue('C' . $row, 'Kelas: ' . $id_kelas);
        $row++;

        // Header kolom
        $sheet->setCellValue('A' . $row, 'Nama Mata Pelajaran');
        $sheet->setCellValue('B' . $row, 'Jam Pelajaran');
        $sheet->setCellValue('C' . $row, 'Topik Pembahasan');
        $sheet->setCellValue('D' . $row, 'Catatan');
        $row++;

        // Atur gaya header
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A' . ($row - 1) . ':D' . ($row - 1))->applyFromArray($headerStyle);
    }

    // Tambahkan data ke tabel
    $sheet->setCellValue('A' . $row, $data['nama_mapel']);
    $sheet->setCellValue('B' . $row, $data['jam_pelajaran']);
    $sheet->setCellValue('C' . $row, $data['topik_pembahasan']);
    $sheet->setCellValue('D' . $row, $data['catatan']);
    $row++;
}

// Atur lebar kolom
foreach (range('A', 'D') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Nama file
$filename = "Jurnal_Kelas_Bulan_" . $bulan . "_Kelas_" . $id_kelas . ".xlsx";

// Simpan file sebagai download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
