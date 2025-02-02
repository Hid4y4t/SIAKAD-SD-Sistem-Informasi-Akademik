<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); // Set header JSON

require_once '../koneksi/koneksi.php';

// Cek koneksi database
if (!$mysqli) {
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi ke database gagal.'
    ]);
    exit;
}

// Tangkap data yang dikirim dari JavaScript
$id_siswa = intval($_POST['id_siswa']);
$selectedMonths = json_decode($_POST['selectedMonths'], true);
$totalBayar = filter_var($_POST['totalBayar'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$keterangan = filter_var($_POST['keterangan'], FILTER_SANITIZE_STRING);
$printNota = isset($_POST['printNota']) && $_POST['printNota'] === '1';

// Log data input untuk debugging
file_put_contents('log_debug.txt', "Data yang diterima: " . print_r($_POST, true) . "\n", FILE_APPEND);

try {
    // Mulai transaksi
    $mysqli->begin_transaction();

    // Simpan data ke tabel `nota`
    $notaQuery = "INSERT INTO nota (id_siswa, jumlah_dibayarkan, keterangan, tanggal_pembayaran) VALUES (?, ?, ?, NOW())";
    $stmtNota = $mysqli->prepare($notaQuery);

    if (!$stmtNota) {
        file_put_contents('log_debug.txt', "Kesalahan pada statement nota: " . $mysqli->error . "\n", FILE_APPEND);
        throw new Exception("Kesalahan pada statement nota: " . $mysqli->error);
    }

    $stmtNota->bind_param("ids", $id_siswa, $totalBayar, $keterangan);
    $stmtNota->execute();
    $notaId = $stmtNota->insert_id;

    // Simpan bulan tagihan ke tabel `detail_nota`
    foreach ($selectedMonths as $month) {
        $detailQuery = "INSERT INTO detail_nota (id_nota, bulan_tagihan) VALUES (?, ?)";
        $stmtDetail = $mysqli->prepare($detailQuery);

        if (!$stmtDetail) {
            file_put_contents('log_debug.txt', "Kesalahan pada statement detail_nota: " . $mysqli->error . "\n", FILE_APPEND);
            throw new Exception("Kesalahan pada statement detail_nota: " . $mysqli->error);
        }

        $stmtDetail->bind_param("is", $notaId, $month);
        $stmtDetail->execute();
    }

    // Commit transaksi
    $mysqli->commit();

    // Berikan respons JSON yang valid
    echo json_encode([
        'success' => true,
        'message' => 'Pembayaran berhasil diproses.',
        'notaId' => $notaId
    ]);

} catch (Exception $e) {
    // Rollback jika terjadi kesalahan
    $mysqli->rollback();
    file_put_contents('log_debug.txt', "Kesalahan pada server: " . $e->getMessage() . "\n", FILE_APPEND);

    // Kembalikan pesan error dalam JSON
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
    ]);
    exit;
}
?>
