<?php
session_start();
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_dana_sharing'])) {
    $id_dana_sharing = intval($_POST['id_dana_sharing']);
    $query = "DELETE FROM dana_sharing WHERE id_dana_sharing = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_dana_sharing);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
} else {
    echo 'error';
}
?>
