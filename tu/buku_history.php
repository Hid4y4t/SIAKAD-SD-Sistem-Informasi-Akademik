<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

// Ambil id_siswa dari parameter URL
$id_siswa = $_GET['id_siswa'];

// Query untuk mengambil data siswa
$querySiswa = "SELECT nama_siswa, kelas FROM siswa WHERE id_siswa = ?";
$stmtSiswa = $mysqli->prepare($querySiswa);
$stmtSiswa->bind_param("i", $id_siswa);
$stmtSiswa->execute();
$resultSiswa = $stmtSiswa->get_result();
$siswa = $resultSiswa->fetch_assoc();

// Jika siswa tidak ditemukan, tampilkan pesan error
if (!$siswa) {
    echo "Siswa tidak ditemukan.";
    exit;
}

// Query untuk mengambil riwayat pembayaran buku berdasarkan id_siswa
$queryHistory = "
    SELECT bh.*, b.jenis_buku, bp.total_tagihan, bp.jumlah_terbayar
    FROM buku_history bh
    JOIN buku_pembayaran bp ON bh.id_pembayaran = bp.id_pembayaran
    JOIN buku b ON bp.id_buku = b.id_buku
    WHERE bp.id_siswa = ?
    ORDER BY bh.tanggal_bayar DESC
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
                <h3>Riwayat Pembayaran Buku - <?php echo htmlspecialchars($siswa['nama_siswa']); ?></h3>
                <p>Kelas: <?php echo htmlspecialchars($siswa['kelas']); ?></p>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Detail Riwayat Pembayaran Buku</h4>
                                    <a href="cetak_excel_buku_history.php?id_siswa=<?php echo $id_siswa; ?>" class="btn btn-success">Cetak ke Excel</a>

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal Bayar</th>
                                                    <th>Jenis Buku</th>
                                                    <th>Total Tagihan</th>
                                                    <th>Jumlah Terbayar</th>
                                                    <th>Jumlah Bayar (Cicilan)</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultHistory->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo date('d-m-Y', strtotime($row['tanggal_bayar'])); ?></td>
                                                    <td><?php echo htmlspecialchars($row['jenis_buku']); ?></td>
                                                    <td>Rp <?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?></td>
                                                    <td>Rp <?php echo number_format($row['jumlah_terbayar'], 0, ',', '.'); ?></td>
                                                    <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include 'root/js.php'; ?>
</body>
</html>

<?php
$stmtSiswa->close();
$stmtHistory->close();
$mysqli->close();
?>
