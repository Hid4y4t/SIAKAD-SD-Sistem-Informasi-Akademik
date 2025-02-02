<?php
require_once '../koneksi/koneksi.php';

if (isset($_POST['id_bebas'])) {
    $id_bebas = intval($_POST['id_bebas']);

    $query = "DELETE FROM siswa_bebas_dana_pengembangan WHERE id_bebas = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_bebas);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Gagal menghapus data.';
    }
} else {
    echo 'ID tidak valid.';
}
?>
