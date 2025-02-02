<?php
require_once '../koneksi/koneksi.php';




// Bulan dan tahun saat ini
$currentMonth = date('n'); // Bulan saat ini dalam angka (1-12)
$currentYear = date('Y');  // Tahun saat ini

// Query untuk mendapatkan siswa yang belum membayar SPP pada bulan ini
$query = "
    SELECT s.id_siswa 
    FROM siswa s
    LEFT JOIN pembayaran_spp p ON s.id_siswa = p.id_siswa AND p.bulan = ? AND p.tahun = ?
    WHERE p.id_pembayaran IS NULL
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $currentMonth, $currentYear);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $id_siswa = $row['id_siswa'];

    // Cek apakah notifikasi sudah ada untuk siswa dan bulan ini
    $checkQuery = "
        SELECT * FROM notifikasi_spp 
        WHERE id_siswa = ? AND bulan_tagihan = ? AND tahun_tagihan = ?
    ";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("iii", $id_siswa, $currentMonth, $currentYear);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        // Tambahkan notifikasi jika belum ada
        $insertQuery = "
            INSERT INTO notifikasi_spp (id_siswa, bulan_tagihan, tahun_tagihan) 
            VALUES (?, ?, ?)
        ";
        $insertStmt = $mysqli->prepare($insertQuery);
        $insertStmt->bind_param("iii", $id_siswa, $currentMonth, $currentYear);
        $insertStmt->execute();
    }
}

echo "Notifikasi SPP untuk siswa yang belum membayar telah diperbarui.";


require 'vendor/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Query untuk mendapatkan data siswa yang belum membayar SPP
$query = "
    SELECT s.nis, s.nama_siswa, s.kelas, n.bulan_tagihan, n.tahun_tagihan
    FROM notifikasi_spp n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    ORDER BY n.tahun_tagihan DESC, n.bulan_tagihan DESC
";
$result = $mysqli->query($query);

if (!$result) {
    die("Query Error: " . $mysqli->error);
}

// Membuat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan header kolom
$sheet->setCellValue('A1', 'NIS');
$sheet->setCellValue('B1', 'Nama Siswa');
$sheet->setCellValue('C1', 'Kelas');
$sheet->setCellValue('D1', 'Bulan Tagihan');
$sheet->setCellValue('E1', 'Tahun Tagihan');

// Mengisi data dari database ke file Excel
$rowNumber = 2; // Mulai dari baris kedua
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['nis']);
    $sheet->setCellValue('B' . $rowNumber, $row['nama_siswa']);
    $sheet->setCellValue('C' . $rowNumber, $row['kelas']);
    $sheet->setCellValue('D' . $rowNumber, $row['bulan_tagihan']);
    $sheet->setCellValue('E' . $rowNumber, $row['tahun_tagihan']);
    $rowNumber++;
}

// Mengatur nama file dan header untuk download file Excel
$fileName = "Siswa_Belum_Bayar_SPP.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");
header('Cache-Control: max-age=0');

// Menyimpan file Excel ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
