<?php
require_once '../koneksi/koneksi.php';

// Ambil parameter dari request
$id_guru = $_GET['id_guru'] ?? null;
$bulan = $_GET['bulan'] ?? null;

// Periksa parameter yang diperlukan
if (empty($id_guru) || empty($bulan)) {
    echo json_encode(["error" => "Missing parameters: id_guru or bulan"]);
    exit;
}

// Query untuk mengambil data absensi
$query = "
    SELECT waktu_masuk, waktu_pulang, tanggal 
    FROM absensi_guru 
    WHERE id_guru = ? AND DATE_FORMAT(tanggal, '%Y-%m') = ?
    ORDER BY tanggal ASC
";

$stmt = $mysqli->prepare($query);

// Periksa apakah query berhasil dipersiapkan
if (!$stmt) {
    echo json_encode(["error" => "Query preparation failed: " . $mysqli->error]);
    exit;
}

// Bind parameter dan eksekusi
$stmt->bind_param("is", $id_guru, $bulan);
if (!$stmt->execute()) {
    echo json_encode(["error" => "Query execution failed: " . $stmt->error]);
    exit;
}

// Ambil hasil query
$result = $stmt->get_result();

// Siapkan data untuk dikirim ke frontend
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Kembalikan data sebagai JSON
echo json_encode($data);
