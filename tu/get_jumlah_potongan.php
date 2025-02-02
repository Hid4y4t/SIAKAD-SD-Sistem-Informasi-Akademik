<?php
require '../koneksi/koneksi.php';

if (isset($_GET['id_potongan'])) {
    $id_potongan = $_GET['id_potongan'];
    
    // Query untuk mendapatkan jumlah potongan berdasarkan id_potongan
    $query = "SELECT potongan FROM jenis_potongan_spp WHERE id_potongan = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_potongan);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['jumlah' => $row['potongan']]);
    } else {
        echo json_encode(['jumlah' => 0]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['jumlah' => 0]);
}

$mysqli->close();
?>
