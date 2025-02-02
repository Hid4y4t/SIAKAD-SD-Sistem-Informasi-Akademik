<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

// Cek apakah `id_siswa` tersedia di URL
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;
if ($id_siswa <= 0) {
    die("ID Siswa tidak valid.");
}

// Mengambil data siswa berdasarkan `id_siswa`
$querySiswa = "SELECT * FROM siswa WHERE id_siswa = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("i", $id_siswa);
$stmtSiswa->execute();
$siswaData = $stmtSiswa->get_result()->fetch_assoc();
$stmtSiswa->close();

if (!$siswaData) {
    die("Data siswa tidak ditemukan.");
}

// Mengambil data history pembayaran dana sharing untuk siswa yang dipilih
$queryHistory = "SELECT * FROM dana_sharing_history WHERE id_siswa = ? ORDER BY tanggal_bayar DESC";
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
                <h3>History Pembayaran Dana Sharing</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Pembayaran - <?php echo htmlspecialchars($siswaData['nama_siswa']); ?></h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal Pembayaran</th>
                                                    <th>Jumlah Bayar</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($resultHistory->num_rows > 0): ?>
                                                    <?php while ($row = $resultHistory->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_bayar'])); ?></td>
                                                            <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                                            <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">Belum ada history pembayaran.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button class="btn btn-success mt-3" onclick="exportToExcel(<?php echo $id_siswa; ?>)">Cetak ke Excel</button>
                                    <button class="btn btn-secondary mt-3" onclick="window.history.back()">Kembali</button>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include 'root/js.php'; ?>

    <!-- JavaScript untuk Export ke Excel -->
    <script>
        function exportToExcel(idSiswa) {
            window.location.href = `export_dana_sharing_history.php?id_siswa=${idSiswa}`;
        }
    </script>
</body>
</html>

<?php
$stmtHistory->close();
?>
