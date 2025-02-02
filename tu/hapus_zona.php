<?php
session_start();

require_once '../koneksi/koneksi.php';

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_zona'])) {
    $id_zona = intval($_POST['id_zona']);

    // Prepare and execute delete query
    $query = "DELETE FROM zona_transportasi WHERE id_zona = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_zona);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $mysqli->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
