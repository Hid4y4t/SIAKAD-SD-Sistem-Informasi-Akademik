<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    echo json_encode(["error" => "Unauthorized access."]);
    exit;
}

if (isset($_GET['id'])) {
    $id_nominal = intval($_GET['id']);

    $query = "SELECT * FROM dana_sharing_nominal WHERE id_nominal = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_nominal);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Data not found."]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "ID not provided."]);
}
?>
