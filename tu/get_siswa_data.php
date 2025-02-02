<?php
require '../koneksi/koneksi.php';

if (isset($_GET['nis'])) {
    $nis = $_GET['nis'];
    $query = "SELECT nama_siswa FROM siswa WHERE nis = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['nama_siswa' => $row['nama_siswa']]);
    } else {
        echo json_encode([]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'NIS tidak ditemukan']);
}

$mysqli->close();
?>
