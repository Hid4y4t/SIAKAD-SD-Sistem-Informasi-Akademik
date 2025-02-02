<?php
require_once '../koneksi/koneksi.php';

// Ambil parameter dari URL
$id_kelas = $_GET['id_kelas'];
$id_guru = $_GET['id_guru'];
$mapel = $_GET['mapel'];

// Query untuk mengambil data nilai berdasarkan parameter
$query = "
    SELECT 
        s.nama_siswa, 
        n.nilai, 
        n.catatan, 
        n.tanggal 
    FROM 
        nilai_ulangan_harian n
    JOIN 
        siswa s ON n.id_siswa = s.id_siswa
    WHERE 
        n.id_kelas = ? AND n.id_guru = ? AND n.mapel = ?
    ORDER BY n.tanggal DESC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iis", $id_kelas, $id_guru, $mapel);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Kembalikan data dalam format JSON
echo json_encode($data);
?>
