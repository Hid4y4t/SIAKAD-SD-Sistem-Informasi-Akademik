<?php
require '../koneksi/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_beasiswa = $_POST['id_beasiswa'];
    $jenis_beasiswa = $_POST['jenis_beasiswa'];

    $stmt = $mysqli->prepare("UPDATE beasiswa SET jenis_beasiswa = ? WHERE id_beasiswa = ?");
    $stmt->bind_param("ii", $jenis_beasiswa, $id_beasiswa);

    if ($stmt->execute()) {
        header("Location: halaman_beasiswa.php?status=updated");
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}
?>
