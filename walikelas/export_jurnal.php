<?php
require_once '../koneksi/koneksi.php';
require_once '../tu/vendor/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['tanggal']) || !isset($_GET['id_kelas'])) {
    die("Parameter tidak valid.");
}

$tanggal = $_GET['tanggal'];
$id_kelas = $_GET['id_kelas'];

// Ambil data jurnal kelas berdasarkan tanggal dan id_kelas
$query = "
    SELECT 
        nama_mapel, 
        jam_pelajaran, 
        topik_pembahasan, 
        catatan 
    FROM 
        jurnal_kelas 
    WHERE 
        tanggal = ? AND id_kelas = ?
    ORDER BY jam_pelajaran ASC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $tanggal, $id_kelas);
$stmt->execute();
$result = $stmt->get_result();

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Atur judul file Excel
$sheet->setTitle("Jurnal Kelas");

// Header
$sheet->setCellValue('A1', 'Jurnal Kelas');
$sheet->setCellValue('A2', 'Tanggal: ' . $tanggal);
$sheet->setCellValue('A3', 'Kelas ID: ' . $id_kelas);
$sheet->mergeCells('A1:D1');

// Header kolom
$sheet->setCellValue('A5', 'Mata Pelajaran');
$sheet->setCellValue('B5', 'Jam Pelajaran');
$sheet->setCellValue('C5', 'Topik Pembahasan');
$sheet->setCellValue('D5', 'Catatan');

// Atur gaya header
$headerStyle = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];
$sheet->getStyle('A5:D5')->applyFromArray($headerStyle);

// Isi data
$row = 6; // Mulai dari baris ke-6
while ($data = $result->fetch_assoc()) {
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
$filename = "Jurnal_Kelas_" . $tanggal . "_Kelas_" . $id_kelas . ".xlsx";

// Simpan file sebagai download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
