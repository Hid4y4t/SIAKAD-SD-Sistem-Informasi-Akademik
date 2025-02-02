<?php
require '../koneksi/koneksi.php';

if (isset($_GET['id_beasiswa'])) {
    $id_beasiswa = $_GET['id_beasiswa'];
    $stmt = $mysqli->prepare("SELECT b.id_beasiswa, s.nis AS nis_siswa, b.jenis_beasiswa, jb.potongan AS jumlah 
                              FROM beasiswa b 
                              JOIN siswa s ON b.id_siswa = s.id_siswa 
                              JOIN jenis_beasiswa jb ON b.jenis_beasiswa = jb.id_beasiswa_js 
                              WHERE b.id_beasiswa = ?");
    $stmt->bind_param("i", $id_beasiswa);
    $stmt->execute();
    $result = $stmt->get_result();
    $beasiswa = $result->fetch_assoc();

    echo json_encode($beasiswa);
}
?>
