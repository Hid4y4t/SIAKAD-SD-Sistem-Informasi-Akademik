<?php
require_once '../koneksi/koneksi.php';

$id_nominal = $_POST['id_nominal'];

// Hapus data dari tabel dana_sharing_nominal berdasarkan id_nominal
$query = "DELETE FROM dana_sharing_nominal WHERE id_nominal = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_nominal);

if ($stmt->execute()) {
    echo "Data dana sharing nominal berhasil dihapus.";
} else {
    echo "Gagal menghapus data dana sharing nominal.";
}
$stmt->close();
$mysqli->close();
?>
