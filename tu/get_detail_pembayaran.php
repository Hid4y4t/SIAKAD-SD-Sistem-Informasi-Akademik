<?php
require '../koneksi/koneksi.php';

$id_pembayaran = $_POST['id_pembayaran'];
$query = "
    SELECT s.nama_siswa, s.kelas, b.jenis_buku, bp.total_tagihan, bp.jumlah_terbayar, bp.status 
    FROM buku_pembayaran bp
    JOIN siswa s ON bp.id_siswa = s.id_siswa
    JOIN buku b ON bp.id_buku = b.id_buku
    WHERE bp.id_pembayaran = ?
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_pembayaran);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>
