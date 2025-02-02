<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';
echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include 'root/menu.php'; ?>
        <div id="main">
            <div class="page-heading">
                <h3>Data Tabungan Siswa</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <a href="#" class="btn icon icon-left btn-primary"><i data-feather="edit"></i> Tambah Tabungan Baru</a>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive datatable-minimal">
                                        <?php
                                        $query = "SELECT * FROM tabungan_siswa ORDER BY id_tabungan DESC";
                                        $result = $mysqli->query($query);

                                        if (!$result) {
                                            die("Query Error: " . $mysqli->error);
                                        }
                                        ?>

                                        <table class="table" id="table2">
                                            <thead>
                                                <tr>
                                                    <th>ID Siswa</th>
                                                    <th>Tanggal</th>
                                                    <th>Waktu</th>
                                                    <th>Pemasukan</th>
                                                    <th>Pengeluaran</th>
                                                    <th>Saldo</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['id_siswa']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['waktu']); ?></td>
                                                        <td><?php echo number_format($row['pemasukan'], 2, ',', '.'); ?></td>
                                                        <td><?php echo number_format($row['pengeluaran'], 2, ',', '.'); ?></td>
                                                        <td><?php echo number_format($row['saldo'], 2, ',', '.'); ?></td>
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
