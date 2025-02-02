<?php
require '../koneksi/koneksi.php';
session_start();

if (!isset($_POST['type']) || !isset($_SESSION['id_guru'])) {
    echo "Invalid Request";
    exit();
}

$id_guru = $_SESSION['id_guru'];
$type = $_POST['type'];

date_default_timezone_set('Asia/Jakarta');
$mysqli->query("SET time_zone = '+07:00'");

if ($type === 'masuk') {
    // Absensi masuk
    $query = "INSERT INTO absensi_guru (id_guru, tanggal, waktu_masuk, status, created_at, updated_at)
              VALUES (?, CURDATE(), CURRENT_TIMESTAMP, 'Hadir', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_guru);
    if ($stmt->execute()) {
        echo "Absensi Masuk Berhasil";
    } else {
        echo "Gagal Absensi Masuk: " . $mysqli->error;
    }
} elseif ($type === 'pulang') {
    // Absensi pulang
    $query = "UPDATE absensi_guru 
              SET waktu_pulang = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP 
              WHERE id_guru = ? AND tanggal = CURDATE()";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_guru);
    if ($stmt->execute()) {
        echo "Absensi Pulang Berhasil";
    } else {
        echo "Gagal Absensi Pulang: " . $mysqli->error;
    }
}

exit();
?>
