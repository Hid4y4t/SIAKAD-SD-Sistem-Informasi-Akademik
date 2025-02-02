<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Mengambil data pembayaran dari tabel `ppdb_history`, menampilkan 500 data terakhir
$queryHistory = "
    SELECT s.nama_siswa, s.kelas, ph.tanggal_bayar, ph.id_history, ph.jumlah_bayar, ph.metode_pembayaran
    FROM ppdb_history ph
    JOIN ppdb_pembayaran pp ON ph.id_pembayaran = pp.id_pembayaran
    JOIN siswa s ON pp.id_siswa = s.id_siswa
    ORDER BY ph.tanggal_bayar DESC
    LIMIT 500
";
$resultHistory = $mysqli->query($queryHistory);

if (!$resultHistory) {
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
                <h3>Riwayat Pembayaran PPDB</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- Tabel Riwayat Pembayaran -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Pembayaran PPDB</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Kolom Pencarian -->
                                    <div class="input-group mb-3">
                                        <input type="text" id="searchInput" placeholder="Cari data..." class="form-control" onkeyup="searchTable()">
                                    </div>
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: scroll;">
                                        <table class="table table-bordered mb-0" id="tableHistory">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Kelas</th>
                                                    <th>Tanggal Bayar</th>
                                                    <th>Jumlah Bayar</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultHistory->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($row['tanggal_bayar'])); ?></td>
                                                    <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                                    <td>
                                                        <!-- Tombol Cetak Nota -->
                                                        <a href="cetak_nota_ppdb.php?id_history=<?php echo $row['id_history']; ?>" target="_blank" class="btn btn-info btn-sm">Cetak</a>
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
        function searchTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("tableHistory");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let tdArray = tr[i].getElementsByTagName("td");
                let found = false;

                for (let j = 0; j < tdArray.length; j++) {
                    if (tdArray[j]) {
                        const textValue = tdArray[j].textContent || tdArray[j].innerText;
                        if (textValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                tr[i].style.display = found ? "" : "none";
            }
        }
    </script>
</body>
</html>
