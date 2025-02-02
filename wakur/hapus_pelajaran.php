<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit;
}

// Periksa apakah parameter id_pelajaran ada
if (!isset($_GET['id_pelajaran']) || !is_numeric($_GET['id_pelajaran'])) {
    header("Location: mapel.php");
    exit;
}

$id_pelajaran = $_GET['id_pelajaran'];

// Hapus pelajaran berdasarkan id_pelajaran
$query = "DELETE FROM pelajaran WHERE id_pelajaran = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_pelajaran);

if ($stmt->execute()) {
    // Redirect ke halaman detail_mapel dengan pesan sukses
    if (isset($_GET['id_kelas'])) {
        $id_kelas = $_GET['id_kelas'];
        header("Location: detail_mapel.php?id_kelas=$id_kelas&success=Pelajaran berhasil dihapus.");
    } else {
        header("Location: mapel.php?success=Pelajaran berhasil dihapus.");
    }
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>
