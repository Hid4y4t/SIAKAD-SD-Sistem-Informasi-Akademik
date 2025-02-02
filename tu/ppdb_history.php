<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Menangkap `id_siswa` dari URL
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;

// Validasi `id_siswa` untuk memastikan id valid
if ($id_siswa <= 0) {
    echo "ID Siswa tidak valid.";
    exit;
}

// Mengambil data siswa berdasarkan `id_siswa`
$siswaQuery = "SELECT nama_siswa, kelas, nis FROM siswa WHERE id_siswa = ?";
$stmt = $mysqli->prepare($siswaQuery);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$siswaData = $stmt->get_result()->fetch_assoc();

if (!$siswaData) {
    echo "Siswa tidak ditemukan.";
    exit;
}

// Mengambil riwayat pembayaran berdasarkan `id_siswa`
$queryHistory = "
    SELECT ph.tanggal_bayar, ph.jumlah_bayar, ph.metode_pembayaran, ph.keterangan
    FROM ppdb_history ph
    JOIN ppdb_pembayaran pp ON ph.id_pembayaran = pp.id_pembayaran
    WHERE pp.id_siswa = ?
    ORDER BY ph.tanggal_bayar DESC
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
                <h3>Riwayat Pembayaran PPDB</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- Tabel Riwayat Pembayaran PPDB -->
                        <section class="section">
                            <div class="card">
                            <div class="card-header">
    <h4 class="card-title">Data Pembayaran PPDB</h4>
    <a href="export_ppdb_history.php?id_siswa=<?php echo $id_siswa; ?>" class="btn btn-success">Cetak ke Excel</a>
</div>

                                <div class="card-body">
                                    <!-- Informasi Siswa -->
                                    <p><strong>Nama Siswa:</strong> <?php echo htmlspecialchars($siswaData['nama_siswa']); ?></p>
                                    <p><strong>Kelas:</strong> <?php echo htmlspecialchars($siswaData['kelas']); ?></p>
                                    <p><strong>NIS:</strong> <?php echo htmlspecialchars($siswaData['nis']); ?></p>
                                    
                                    <!-- Tabel Riwayat Pembayaran -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="tableHistory">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal Bayar</th>
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
                                                <?php if ($resultHistory->num_rows == 0): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">Belum ada riwayat pembayaran.</td>
                                                    </tr>
                                                <?php endif; ?>
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
