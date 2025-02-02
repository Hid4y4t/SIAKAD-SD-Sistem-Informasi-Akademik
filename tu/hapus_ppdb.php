<?php
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pembayaran = $_POST['id_pembayaran'];

    // Query untuk menghapus data berdasarkan ID
    $queryDelete = "DELETE FROM ppdb_pembayaran WHERE id_pembayaran = ?";
    $stmt = $mysqli->prepare($queryDelete);
    $stmt->bind_param("i", $id_pembayaran);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Data berhasil dihapus.']);
    } else {
        echo json_encode(['message' => 'Gagal menghapus data.']);
    }
}
?>
