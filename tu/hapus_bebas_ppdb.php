<?php
require_once '../koneksi/koneksi.php';

// Pastikan `id_bebas` diterima dari permintaan AJAX
if (!isset($_POST['id_bebas'])) {
    echo json_encode(['error' => 'ID tidak ditemukan.']);
    exit;
}

$id_bebas = intval($_POST['id_bebas']);

// Hapus data dari tabel `siswa_bebas_ppdb`
$query = "DELETE FROM siswa_bebas_ppdb WHERE id_bebas = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_bebas);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Data berhasil dihapus.']);
} else {
    echo json_encode(['error' => 'Gagal menghapus data.']);
}
$stmt->close();
$mysqli->close();
