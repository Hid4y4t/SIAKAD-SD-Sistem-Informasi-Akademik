<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $id_nominal = isset($_POST['id_nominal']) ? intval($_POST['id_nominal']) : 0;
    $kelas = isset($_POST['kelas']) ? $_POST['kelas'] : '';
    $angkatan = isset($_POST['angkatan']) ? $_POST['angkatan'] : '';
    $semester = isset($_POST['semester']) ? $_POST['semester'] : '';
    $jumlah_tagihan = isset($_POST['jumlah_tagihan']) ? floatval($_POST['jumlah_tagihan']) : 0;
    $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : NULL;

    // Memastikan ID nominal valid
    if ($id_nominal > 0) {
        // Query update
        $query = "UPDATE dana_sharing_nominal 
                  SET kelas = ?, angkatan = ?, semester = ?, jumlah_tagihan = ?, keterangan = ? 
                  WHERE id_nominal = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssisi", $kelas, $angkatan, $semester, $jumlah_tagihan, $keterangan, $id_nominal);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Data Nominal berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui data nominal.";
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "ID Nominal tidak valid.";
    }

    header("Location: setting_sharing.php");
    exit;
} else {
    header("Location: setting_sharing.php");
    exit;
}
?>
