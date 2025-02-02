<?php
require_once '../koneksi/koneksi.php';

if (isset($_GET['id_nominal'])) {
    $id_nominal = intval($_GET['id_nominal']);
    
    // Query untuk mengambil data nominal berdasarkan id_nominal
    $query = "SELECT id_nominal, angkatan, jumlah_total FROM ppdb_nominal WHERE id_nominal = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_nominal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Cek apakah data ditemukan
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'id_nominal' => $data['id_nominal'],
            'angkatan' => $data['angkatan'],
            'jumlah_total' => $data['jumlah_total']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Data tidak ditemukan']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID Nominal tidak ditemukan']);
}
?>
