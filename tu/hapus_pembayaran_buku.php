<?php
session_start();

require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit;
}

// Ambil `id_pembayaran` dari POST
$id_pembayaran = $_POST['id_pembayaran'] ?? null;

// Validasi `id_pembayaran`
if (empty($id_pembayaran)) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada data pembayaran yang dipilih.']);
    exit;
}

// Jika `id_pembayaran` adalah array, maka lakukan penghapusan banyak data
if (is_array($id_pembayaran)) {
    // Buat query untuk menghapus banyak data dengan `IN`
    $placeholders = implode(',', array_fill(0, count($id_pembayaran), '?'));
    $query = "DELETE FROM buku_pembayaran WHERE id_pembayaran IN ($placeholders)";
    $stmt = $mysqli->prepare($query);

    // Bind parameter secara dinamis
    $stmt->bind_param(str_repeat('i', count($id_pembayaran)), ...$id_pembayaran);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Data pembayaran terpilih berhasil dihapus.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data pembayaran terpilih.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
}

$mysqli->close();
?>
