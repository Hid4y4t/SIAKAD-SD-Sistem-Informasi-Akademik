<?php
require '../koneksi/koneksi.php';
require 'vendor/vendor/autoload.php'; // Autoload dari Composer

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_siswa'])) {
    $file = $_FILES['file_siswa']['tmp_name'];

    try {
        // Membaca file Excel
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Mendapatkan jumlah baris terakhir yang memiliki data
        $highestRow = $worksheet->getHighestRow();

        // Memulai iterasi dari baris ke-5 dan kolom ke-B
        for ($row = 5; $row <= $highestRow; $row++) {
            $nis = $worksheet->getCell("B{$row}")->getValue();
            $nama_siswa = $worksheet->getCell("C{$row}")->getValue();
            $email_siswa = $worksheet->getCell("D{$row}")->getValue();
            $telepon_siswa = $worksheet->getCell("E{$row}")->getValue();
            $alamat_siswa = $worksheet->getCell("F{$row}")->getValue();
            $tanggal_lahir = $worksheet->getCell("G{$row}")->getValue();
            $kelas = $worksheet->getCell("H{$row}")->getValue();
            $angkatan = $worksheet->getCell("I{$row}")->getValue();
            $jenis_kelamin = $worksheet->getCell("J{$row}")->getValue();
            $status = $worksheet->getCell("K{$row}")->getValue();

            // Konversi tanggal jika diperlukan (dari format numerik Excel ke format 'YYYY-MM-DD')
            if (Date::isDateTime($worksheet->getCell("G{$row}"))) {
                $tanggal_lahir = Date::excelToDateTimeObject($tanggal_lahir)->format('Y-m-d');
            }

          // Pastikan urutan kolom sesuai dengan database
          $stmt = $mysqli->prepare("INSERT INTO siswa (nis, nama_siswa, email_siswa, telepon_siswa, alamat_siswa, tanggal_lahir, kelas, angkatan, jenis_kelamin, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("ssssssissi", $nis, $nama_siswa, $email_siswa, $telepon_siswa, $alamat_siswa, $tanggal_lahir, $kelas, $angkatan, $jenis_kelamin,  $status);
          $stmt->execute();
        }
        
        header("Location: siswa.php?import_success=1");
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: siswa.php?import_error=1");
}
?>
