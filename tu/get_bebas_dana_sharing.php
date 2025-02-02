<?php
require_once '../koneksi/koneksi.php';

if (isset($_GET['id_bebas'])) {
    $id_bebas = intval($_GET['id_bebas']);

    $query = "SELECT bds.*, s.nama_siswa, s.kelas 
              FROM siswa_bebas_dana_sharing bds 
              JOIN siswa s ON bds.id_siswa = s.id_siswa 
              WHERE bds.id_bebas = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_bebas);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Data tidak ditemukan.']);
    }
} else {
    echo json_encode(['error' => 'ID tidak valid.']);
}
?>
