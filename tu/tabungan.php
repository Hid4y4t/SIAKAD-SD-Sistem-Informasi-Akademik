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
            <div class="page-heading mb-4">
                <h3>Data Tabungan Siswa</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalTambahTabungan">
                                            Tambah Tabungan Baru
                                        </button>
                                    </h5>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                    <?php
$query = "
    SELECT ts.id_siswa, ts.tanggal, ts.waktu, ts.pemasukan, ts.pengeluaran, ts.saldo, s.nama_siswa
    FROM tabungan_siswa ts
    INNER JOIN (
        SELECT id_siswa, MAX(id_tabungan) AS last_transaction
        FROM tabungan_siswa
        GROUP BY id_siswa
    ) last_trans ON ts.id_siswa = last_trans.id_siswa AND ts.id_tabungan = last_trans.last_transaction
    JOIN siswa s ON ts.id_siswa = s.id_siswa
    ORDER BY ts.id_tabungan DESC
";
$result = $mysqli->query($query);

if (!$result) {
    die("Query Error: " . $mysqli->error);
}
?>

<table class="table table-striped align-middle" id="table2">
    <thead>
        <tr>
         
            <th>Nama Siswa</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Pemasukan</th>
            <th>Pengeluaran</th>
            <th>Saldo</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
           
            <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td> <!-- Menampilkan nama siswa -->
            <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
            <td><?php echo htmlspecialchars($row['waktu']); ?></td>
            <td><span class="badge bg-success">Rp <?php echo number_format($row['pemasukan'], 2, ',', '.'); ?></span></td>
            <td><span class="badge bg-danger">Rp <?php echo number_format($row['pengeluaran'], 2, ',', '.'); ?></span></td>
            <td><span class="badge bg-warning">Rp <?php echo number_format($row['saldo'], 2, ',', '.'); ?></span></td>
            <td>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalSetor<?php echo $row['id_siswa']; ?>">Setor</button>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalPenarikan<?php echo $row['id_siswa']; ?>">Penarikan</button>
                <a href="detail_tabungan.php?id_siswa=<?php echo $row['id_siswa']; ?>" class="btn btn-info btn-sm">Detail</a>
            </td>
        </tr>

        <!-- Modal Setor -->
        <div class="modal fade" id="modalSetor<?php echo $row['id_siswa']; ?>" tabindex="-1" aria-labelledby="modalSetorLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="proses_setor.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Setor Tabungan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_siswa" value="<?php echo $row['id_siswa']; ?>">
                            <p><strong>Nama:</strong> <?php echo htmlspecialchars($row['nama_siswa']); ?></p> <!-- Menampilkan nama siswa di modal -->
                            <div class="mb-3">
                                <label for="jumlah_setor" class="form-label">Jumlah Setor</label>
                                <input type="number" class="form-control" name="jumlah_setor" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Penarikan -->
        <div class="modal fade" id="modalPenarikan<?php echo $row['id_siswa']; ?>" tabindex="-1" aria-labelledby="modalPenarikanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="proses_penarikan.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Penarikan Tabungan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_siswa" value="<?php echo $row['id_siswa']; ?>">
                            <p><strong>Nama:</strong> <?php echo htmlspecialchars($row['nama_siswa']); ?></p> <!-- Menampilkan nama siswa di modal -->
                            <div class="mb-3">
                                <label for="jumlah_penarikan" class="form-label">Jumlah Penarikan</label>
                                <input type="number" class="form-control" name="jumlah_penarikan" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" name="keterangan"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php endwhile; ?>
    </tbody>
</table>


                                    </div>
                                </div>
                            </div>

                            <!-- Modal Tambah Tabungan Baru -->
                            <div class="modal fade" id="modalTambahTabungan" tabindex="-1"
                                aria-labelledby="modalTambahTabunganLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="proses_tambah_tabungan.php" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tambah Tabungan Siswa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="id_siswa" class="form-label">NIS</label>
                                                    <input type="number" class="form-control" name="nis" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="jumlah_setor" class="form-label">Jumlah Setor
                                                        Awal</label>
                                                    <input type="number" class="form-control" name="pemasukan" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="keterangan" class="form-label">Keterangan</label>
                                                    <textarea class="form-control" name="keterangan"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
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