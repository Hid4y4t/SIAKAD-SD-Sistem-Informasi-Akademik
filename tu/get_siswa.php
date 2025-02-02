<?php
require '../koneksi/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $siswa = $result->fetch_assoc();
    echo json_encode($siswa);
}
?>
