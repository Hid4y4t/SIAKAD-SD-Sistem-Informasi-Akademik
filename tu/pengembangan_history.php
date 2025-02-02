<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

// Ambil id_siswa dari URL
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;

// Validasi id_siswa
if ($id_siswa <= 0) {
    echo "ID Siswa tidak valid.";
    exit;
}

// Ambil data siswa
$querySiswa = "SELECT nama_siswa, kelas FROM siswa WHERE id_siswa = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("i", $id_siswa);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result()->fetch_assoc();

// Validasi keberadaan siswa
if (!$resultSiswa) {
    echo "Data siswa tidak ditemukan.";
    exit;
}

// Ambil data riwayat pembayaran dana pengembangan dari tabel dana_pengembangan_history berdasarkan id_siswa
$queryHistory = "
    SELECT h.tanggal_bayar, h.jumlah_bayar, h.metode_pembayaran, h.keterangan 
    FROM dana_pengembangan_history h
    JOIN dana_pengembangan dp ON h.id_dana_pengembangan = dp.id_dana_pengembangan
    WHERE dp.id_siswa = ?
    ORDER BY h.tanggal_bayar DESC
";
$stmtHistory = $mysqli->prepare($queryHistory);
$stmtHistory->bind_param("i", $id_siswa);
$stmtHistory->execute();
$resultHistory = $stmtHistory->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include 'root/menu.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Riwayat Pembayaran Dana Pengembangan</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Nama Siswa: <?php echo htmlspecialchars($resultSiswa['nama_siswa']); ?></h4>
                                <p>Kelas: <?php echo htmlspecialchars($resultSiswa['kelas']); ?></p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Pembayaran</th>
                                                <th>Jumlah Bayar</th>
                                                <th>Metode Pembayaran</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $resultHistory->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo date('d-m-Y', strtotime($row['tanggal_bayar'])); ?></td>
                                                <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                                <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                                
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                        <a href="export_pengembangan_history.php?id_siswa=<?php echo $id_siswa; ?>" class="btn btn-success">Cetak ke Excel</a>

                                    </table>
                                    <?php if ($resultHistory->num_rows == 0): ?>
                                        <p class="text-center mt-3">Tidak ada data riwayat pembayaran.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php include 'root/js.php'; ?>
</body>
</html>

<?php
// Tutup statement dan koneksi
$stmtSiswa->close();
$stmtHistory->close();
$mysqli->close();
?>
