<?php
require_once '../koneksi/koneksi.php';

$nis = isset($_GET['nis']) ? $_GET['nis'] : '';

// Ambil data siswa berdasarkan NIS
$querySiswa = "
    SELECT s.nis, s.nama_siswa, s.kelas
    FROM siswa s
    WHERE s.nis = ?
";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("s", $nis);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();
$siswa = $resultSiswa->fetch_assoc();

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

// Ambil riwayat pembayaran siswa dari tabel `nota`
$queryDetailPembayaran = "
    SELECT n.jenis_pembayaran, n.jenis_potongan, n.jumlah_dibayarkan, n.keterangan, n.tanggal_pembayaran
    FROM nota n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    WHERE s.nis = ?
    ORDER BY n.tanggal_pembayaran DESC
";
$stmt = $mysqli->prepare($queryDetailPembayaran);
$stmt->bind_param("s", $nis);
$stmt->execute();
$resultDetailPembayaran = $stmt->get_result();

if (!$resultDetailPembayaran) {
    die("Query Error: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h3>Detail Riwayat Pembayaran SPP</h3>
            </div>
            <div class="card-body">
                <!-- Informasi Identitas Siswa -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <p><strong>NIS:</strong> <?php echo htmlspecialchars($siswa['nis']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Nama:</strong> <?php echo htmlspecialchars($siswa['nama_siswa']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Kelas:</strong> <?php echo htmlspecialchars($siswa['kelas']); ?></p>
                    </div>
                </div>

                <!-- Tabel Riwayat Pembayaran -->
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis Pembayaran</th>
                            <th>Jenis Potongan</th>
                            <th>Jumlah Dibayarkan</th>
                            <th>Keterangan</th>
                            <th>Tanggal Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultDetailPembayaran->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['jenis_pembayaran'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_potongan'] ?? ''); ?></td>
                            <td>Rp <?php echo number_format($row['jumlah_dibayarkan'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['keterangan'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_pembayaran'] ?? ''))); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <!-- Tombol Kembali dan Cetak ke Excel -->
                <div class="text-end">
                    <a href="javascript:history.back()" class="btn btn-primary">Kembali</a>
                    <a href="export_excel_detail_spp.php?nis=<?php echo htmlspecialchars($nis); ?>" class="btn btn-success">Cetak ke Excel</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'root/js.php'; ?>
</body>
</html>
