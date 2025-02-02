<?php
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $angkatan = $_POST['angkatan'];

    // Query untuk mendapatkan siswa berdasarkan angkatan
    $querySiswa = "
        SELECT s.id_siswa, pn.id_nominal, pn.jumlah_total 
        FROM siswa s
        JOIN ppdb_nominal pn ON s.angkatan = pn.angkatan
        LEFT JOIN ppdb_pembayaran pp ON s.id_siswa = pp.id_siswa
        LEFT JOIN siswa_bebas_ppdb sb ON s.id_siswa = sb.id_siswa
        WHERE s.angkatan = ? AND pp.id_siswa IS NULL AND sb.id_siswa IS NULL
    ";
    $stmt = $mysqli->prepare($querySiswa);
    $stmt->bind_param("s", $angkatan);
    $stmt->execute();
    $result = $stmt->get_result();

    // Memulai transaksi
    $mysqli->begin_transaction();
    try {
        while ($row = $result->fetch_assoc()) {
            $id_siswa = $row['id_siswa'];
            $id_nominal = $row['id_nominal'];
            $total_tagihan = $row['jumlah_total'];

            // Menambahkan data ke tabel ppdb_pembayaran
            $queryInsert = "
                INSERT INTO ppdb_pembayaran (id_siswa, id_nominal, total_tagihan, jumlah_terbayar, status)
                VALUES (?, ?, ?, 0, 'Belum Lunas')
            ";
            $stmtInsert = $mysqli->prepare($queryInsert);
            $stmtInsert->bind_param("iid", $id_siswa, $id_nominal, $total_tagihan);
            $stmtInsert->execute();
        }
        $mysqli->commit();
        echo json_encode(['message' => 'Data berhasil ditambahkan.']);
    } catch (Exception $e) {
        $mysqli->rollback();
        echo json_encode(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}
?>
