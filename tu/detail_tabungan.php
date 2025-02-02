<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

// Ambil `id_siswa` dari URL
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;

// Cek apakah `id_siswa` valid
if ($id_siswa <= 0) {
    echo "ID siswa tidak valid.";
    exit;
}

// Ambil data siswa berdasarkan `id_siswa`
$siswa_query = "SELECT * FROM siswa WHERE id_siswa = ?";
$stmt = $mysqli->prepare($siswa_query);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$siswa_result = $stmt->get_result();

// Pastikan data siswa ditemukan
if ($siswa_result->num_rows > 0) {
    $siswa_data = $siswa_result->fetch_assoc();
} else {
    echo "Data siswa tidak ditemukan.";
    exit;
}

// Ambil riwayat tabungan siswa berdasarkan `id_siswa`
$history_query = "SELECT * FROM tabungan_siswa WHERE $id_siswa = ? ORDER BY id_tabungan DESC";
$stmt = $mysqli->prepare($history_query);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$history_result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include 'root/menu.php'; ?>
        <div id="main">
            <div class="page-heading mb-4">
                <h3>Detail Tabungan Siswa</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Riwayat Tabungan: <?php echo htmlspecialchars($siswa_data['nama_siswa']); ?>  <br>(NIS: <?php echo htmlspecialchars($siswa_data['nis']); ?>)</h5>
                                <a href="tabungan.php" class="btn btn-secondary btn-sm">Kembali</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Waktu</th>
                                                <th>Pemasukan</th>
                                                <th>Pengeluaran</th>
                                                <th>Saldo</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($history_result->num_rows > 0): ?>
                                                <?php while ($row = $history_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['waktu']); ?></td>
                                                        <td>Rp <?php echo number_format($row['pemasukan'], 2, ',', '.'); ?></td>
                                                        <td>Rp <?php echo number_format($row['pengeluaran'], 2, ',', '.'); ?></td>
                                                        <td>Rp <?php echo number_format($row['saldo'], 2, ',', '.'); ?></td>
                                                        <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada riwayat tabungan untuk siswa ini.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="cetak_history_excel.php" method="POST">
                                    <input type="hidden" name="id_siswa" value="<?php echo $id_siswa; ?>">
                                    <button type="submit" class="btn btn-success">Cetak Excel</button>
                                </form>
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
