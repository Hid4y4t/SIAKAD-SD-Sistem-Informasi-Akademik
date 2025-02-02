<?php
require '../koneksi/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_beasiswa = $_POST['id_beasiswa'];

    $stmt = $mysqli->prepare("DELETE FROM beasiswa WHERE id_beasiswa = ?");
    $stmt->bind_param("i", $id_beasiswa);

    if ($stmt->execute()) {
        header("Location: halaman_beasiswa.php?status=deleted");
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
}
?>
