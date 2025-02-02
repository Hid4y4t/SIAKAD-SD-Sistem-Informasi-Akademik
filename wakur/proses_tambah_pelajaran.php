<?php
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kelas = $_POST['id_kelas'];
    $nama_pelajaran = $_POST['nama_pelajaran'];
    $id_guru = $_POST['id_guru'];

    $query = "INSERT INTO pelajaran (id_kelas, nama_pelajaran, id_guru) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("isi", $id_kelas, $nama_pelajaran, $id_guru);

    if ($stmt->execute()) {
        header("Location: detail_mapel.php?id_kelas=$id_kelas");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
