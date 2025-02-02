<?php
require '../koneksi/koneksi.php'; // Sesuaikan jalur file koneksi Anda

// Query untuk mendapatkan jumlah siswa berdasarkan jenis kelamin
$query = "SELECT jenis_kelamin, COUNT(*) as jumlah FROM siswa GROUP BY jenis_kelamin";
$result = $mysqli->query($query);

$data = [
    'male' => 0,
    'female' => 0
];

// Menyusun data dari query
while ($row = $result->fetch_assoc()) {
    if ($row['jenis_kelamin'] === 'L') {
        $data['male'] = (int) $row['jumlah'];
    } elseif ($row['jenis_kelamin'] === 'P') {
        $data['female'] = (int) $row['jumlah'];
    }
}

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
