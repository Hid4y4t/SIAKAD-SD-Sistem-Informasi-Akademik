<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Query untuk mendapatkan semua siswa yang belum terdaftar dalam siswa_bebas_dana_pengembangan atau dana_pengembangan
$querySiswa = "
    SELECT s.id_siswa, dpn.id_nominal, dpn.jumlah_total
    FROM siswa s
    JOIN dana_pengembangan_nominal dpn ON s.angkatan = dpn.angkatan
    LEFT JOIN siswa_bebas_dana_pengembangan sb ON s.id_siswa = sb.id_siswa
    WHERE sb.id_siswa IS NULL
";

$resultSiswa = $mysqli->query($querySiswa);

if (!$resultSiswa) {
    die("Query Error: " . $mysqli->error);
}

// Mulai transaksi
$mysqli->begin_transaction();
try {
    while ($row = $resultSiswa->fetch_assoc()) {
        $id_siswa = $row['id_siswa'];
        $id_nominal = $row['id_nominal'];
        $total_tagihan = $row['jumlah_total'];

        // Cek apakah id_siswa sudah ada di tabel dana_pengembangan
        $checkQuery = "SELECT id_siswa FROM dana_pengembangan WHERE id_siswa = ?";
        $stmtCheck = $mysqli->prepare($checkQuery);
        $stmtCheck->bind_param("i", $id_siswa);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows == 0) {
            // Menambahkan data ke tabel dana_pengembangan
            $queryInsert = "
                INSERT INTO dana_pengembangan (id_siswa, id_nominal, total_tagihan, jumlah_terbayar, status)
                VALUES (?, ?, ?, 0, 'Belum Lunas')
            ";
            $stmtInsert = $mysqli->prepare($queryInsert);
            $stmtInsert->bind_param("iid", $id_siswa, $id_nominal, $total_tagihan);
            $stmtInsert->execute();
            $stmtInsert->close();
        } else {
            // Jika id_siswa sudah ada, tidak menambahkan data
            continue;
        }

        $stmtCheck->close();
    }

    // Commit transaksi jika semua berhasil
    $mysqli->commit();
    echo "Data berhasil dimasukkan secara otomatis.";

} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $mysqli->rollback();
    echo "Terjadi kesalahan: " . $e->getMessage();
}

$mysqli->close();
?>
