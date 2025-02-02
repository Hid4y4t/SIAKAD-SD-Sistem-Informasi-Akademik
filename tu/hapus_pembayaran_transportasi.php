<?php
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPembayaran = intval($_POST['id_pembayaran'] ?? 0);
    
    if ($idPembayaran > 0) {
        // Query untuk menghapus data berdasarkan `id_pembayaran`
        $stmt = $mysqli->prepare("DELETE FROM transportasi_pembayaran WHERE id_pembayaran = ?");
        $stmt->bind_param("i", $idPembayaran);
        $result = $stmt->execute();

        if ($result) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menghapus data dari database."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID pembayaran tidak valid."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metode permintaan tidak valid."]);
}
?>
