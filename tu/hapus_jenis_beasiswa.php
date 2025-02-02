<?php
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_beasiswa_js = $_POST['id_beasiswa_js'];

    $stmt = $mysqli->prepare("DELETE FROM jenis_beasiswa WHERE id_beasiswa_js = ?");
    $stmt->bind_param("i", $id_beasiswa_js);
    $stmt->execute();

    header("Location: beasiswa.php"); // Redirect ke halaman sebelumnya
    exit;
}
?>
