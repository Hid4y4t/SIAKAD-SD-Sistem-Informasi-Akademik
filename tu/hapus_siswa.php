<?php
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $mysqli->prepare("DELETE FROM siswa WHERE id_siswa = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);
}
?>
