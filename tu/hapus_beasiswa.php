<?php
require '../koneksi/koneksi.php';

// Cek apakah id_beasiswa diterima
if (isset($_POST['id_beasiswa'])) {
    $id_beasiswa = $_POST['id_beasiswa'];

    // Hapus data beasiswa dari database
    $stmt = $mysqli->prepare("DELETE FROM beasiswa WHERE id_beasiswa = ?");
    $stmt->bind_param("i", $id_beasiswa);

    if ($stmt->execute()) {
        // Redirect ke halaman utama dengan pesan sukses
        header("Location: beasiswa.php?status=sukses");
    } else {
        // Redirect ke halaman utama dengan pesan error
        header("Location: beasiswa.php?status=error");
    }

    $stmt->close();
    $mysqli->close();
} else {
    // Jika id_beasiswa tidak ada, redirect ke halaman utama
    header("Location: beasiswa.php");
}
?>
