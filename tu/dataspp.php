<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Query untuk menampilkan siswa yang belum membayar SPP (ada di tabel `notifikasi_spp`)
$queryBelumBayar = "
    SELECT s.nis, s.nama_siswa, s.kelas, n.bulan_tagihan, n.tahun_tagihan
    FROM notifikasi_spp n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    ORDER BY n.tahun_tagihan DESC, n.bulan_tagihan DESC
";
$resultBelumBayar = $mysqli->query($queryBelumBayar);

if (!$resultBelumBayar) {
    die("Query Error: " . $mysqli->error);
}

// Query untuk menampilkan semua data pembayaran SPP
$queryPembayaranSPP = "
    SELECT s.nis, s.nama_siswa, s.kelas, p.bulan, p.tahun, p.tanggal_bayar, p.jumlah
    FROM pembayaran_spp p
    JOIN siswa s ON p.id_siswa = s.id_siswa
    ORDER BY p.tahun DESC, p.bulan DESC
";
$resultPembayaranSPP = $mysqli->query($queryPembayaranSPP);

if (!$resultPembayaranSPP) {
    die("Query Error: " . $mysqli->error);
}
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
                <h3>Data SPP Siswa</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">

                        <!-- Tabel Siswa Belum Bayar SPP -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Siswa Belum Bayar SPP</h5>
                                    <a href="print_belum_bayar_spp.php" class="btn btn-warning">Print</a>
                                </div>
                                <div class="card-body">
                                    <!-- Kolom Pencarian untuk Tabel Belum Bayar -->
                                    <div class="input-group mb-3">
                                        <input type="text" id="searchBelumBayar" class="form-control" placeholder="Cari Siswa (NIS, Nama, atau Kelas)">
                                    </div>
                                    <div class="table-responsive datatable-minimal">
                                        <table class="table" id="tableBelumBayar">
                                            <thead>
                                                <tr>
                                                    <th>NIS</th>
                                                    <th>Nama</th>
                                                    <th>Kelas</th>
                                                    <th>Bulan Tagihan</th>
                                                    <th>Tahun Tagihan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultBelumBayar->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['bulan_tagihan']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['tahun_tagihan']); ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Tabel Data Pembayaran SPP -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Data Pembayaran SPP</h5>
                                    <form action="print_pembayaran_spp.php" method="POST" class="d-inline">
                                        <label for="bulan_dari">Bulan Dari:</label>
                                        <input type="month" name="bulan_dari" required>
                                        <label for="bulan_sampai">Sampai:</label>
                                        <input type="month" name="bulan_sampai" required>
                                        <button type="submit" class="btn btn-warning">Print</button>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <!-- Kolom Pencarian untuk Tabel Pembayaran -->
                                    <div class="input-group mb-3">
                                        <input type="text" id="searchPembayaran" class="form-control" placeholder="Cari Pembayaran (NIS, Nama, atau Kelas)">
                                    </div>
                                    <div class="table-responsive datatable-minimal">
                                        <table class="table" id="tablePembayaranSPP">
                                            <thead>
                                                <tr>
                                                    <th>NIS</th>
                                                    <th>Nama</th>
                                                    <th>Kelas</th>
                                                    <th>Bulan</th>
                                                    <th>Tahun</th>
                                                    <th>Jumlah</th>
                                                    <th>Tanggal Bayar</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultPembayaranSPP->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['bulan']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['tahun']); ?></td>
                                                    <td>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['tanggal_bayar']); ?></td>
                                                    <td>
                                                        <a href="detail_pembayaran_spp.php?nis=<?php echo $row['nis']; ?>" class="btn btn-info btn-sm">Detail</a>
                                                    </td>
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

    <!-- JavaScript untuk Pencarian Real-time -->
    <script>
        // Fungsi Pencarian untuk Tabel Belum Bayar
        document.getElementById('searchBelumBayar').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.querySelectorAll('#tableBelumBayar tbody tr');

            rows.forEach(function(row) {
                var nis = row.cells[0].textContent.toLowerCase();
                var nama = row.cells[1].textContent.toLowerCase();
                var kelas = row.cells[2].textContent.toLowerCase();

                if (nis.includes(searchValue) || nama.includes(searchValue) || kelas.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Fungsi Pencarian untuk Tabel Pembayaran
        document.getElementById('searchPembayaran').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.querySelectorAll('#tablePembayaranSPP tbody tr');

            rows.forEach(function(row) {
                var nis = row.cells[0].textContent.toLowerCase();
                var nama = row.cells[1].textContent.toLowerCase();
                var kelas = row.cells[2].textContent.toLowerCase();

                if (nis.includes(searchValue) || nama.includes(searchValue) || kelas.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
