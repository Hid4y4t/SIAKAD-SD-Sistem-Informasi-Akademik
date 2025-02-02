<?php
require_once '../koneksi/koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM tagihan_spp WHERE id_tagihan = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Data tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'ID tidak disediakan']);
}
?>
