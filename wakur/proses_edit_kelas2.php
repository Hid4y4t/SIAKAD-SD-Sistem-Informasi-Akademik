<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit;
}

// Proses ganti kelas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_siswa = $_POST['selected_siswa'] ?? [];
    $kelas_baru = $_POST['kelas_baru'];

    if (!empty($selected_siswa) && !empty($kelas_baru)) {
        $ids = implode(',', array_map('intval', $selected_siswa));
        $query_update_kelas = "UPDATE siswa SET kelas = ? WHERE id_siswa IN ($ids)";
        $stmt = $mysqli->prepare($query_update_kelas);
        $stmt->bind_param("s", $kelas_baru);

        if ($stmt->execute()) {
            echo "<script>alert('Kelas berhasil diubah!'); window.location.href='siswa.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Pilih siswa dan kelas baru terlebih dahulu.'); window.history.back();</script>";
    }
}
?>
