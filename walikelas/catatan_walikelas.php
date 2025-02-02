<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil ID wali kelas yang login
$id_guru = $_SESSION['id_guru'];

// Ambil data kelas yang diampu oleh wali kelas
$query_kelas = "SELECT id_kelas, nama_kelas FROM kelas WHERE wali_kelas = ?";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_guru);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();
$kelas = $result_kelas->fetch_assoc();

if (!$kelas) {
    echo "Anda tidak memiliki kelas yang diampu.";
    exit;
}

$id_kelas = $kelas['id_kelas'];
$nama_kelas = $kelas['nama_kelas'];

// Ambil data siswa berdasarkan kelas
$query_siswa = "SELECT * FROM siswa WHERE kelas = ?";
$stmt_siswa = $mysqli->prepare($query_siswa);
$stmt_siswa->bind_param("s", $nama_kelas);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->

<body>
<script src="../assets/static/js/initTheme.js"></script>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include 'root/menu.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Catatan Wali Kelas - <?= htmlspecialchars($nama_kelas); ?></h3>
                <!-- Lihat Catatan -->
                <div class="card mt-4">
                    
                    <div class="card-body">
                        <center> <button class="btn btn-info " onclick="openLihatCatatan()">Lihat Catatan</button>
                            <a href="export_catatan.php" class="btn btn-success ">Cetak ke Excel</a>
                        </center>

                    </div>
                </div>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <!-- Tabel Data Siswa -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Siswa</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php while ($siswa = $result_siswa->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($siswa['nama_siswa']); ?></td>
                                            <td><?= htmlspecialchars($siswa['kelas']); ?></td>
                                            <td>
                                                <!-- Button Tambah Catatan -->
                                                <button class="btn btn-primary btn-sm"
                                                    onclick="openTambahCatatan(<?= $siswa['id_siswa']; ?>, '<?= htmlspecialchars($siswa['nama_siswa']); ?>')">
                                                    Tambah Catatan
                                                </button>
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
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCatatan" tabindex="-1" aria-labelledby="modalCatatanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCatatanLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalCatatanBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Modal untuk Tambah Catatan
    function openTambahCatatan(idSiswa, namaSiswa) {
        document.getElementById('modalCatatanLabel').textContent = `Tambah Catatan untuk ${namaSiswa}`;
        document.getElementById('modalCatatanBody').innerHTML = `
                <form method="POST" action="proses_tambah_catatan.php">
                    <input type="hidden" name="id_siswa" value="${idSiswa}">
                    <input type="hidden" name="id_kelas" value="<?= $id_kelas; ?>">
                    <input type="hidden" name="id_wali_kelas" value="<?= $id_guru; ?>">
                    <div class="mb-3">
                        <label for="permasalahan" class="form-label">Permasalahan</label>
                        <textarea name="permasalahan" id="permasalahan" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="solusi" class="form-label">Solusi</label>
                        <textarea name="solusi" id="solusi" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_tambahan" class="form-label">Catatan Tambahan</label>
                        <textarea name="catatan_tambahan" id="catatan_tambahan" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="dilaporkan_kepada" class="form-label">Dilaporkan Kepada</label>
                        <select name="dilaporkan_kepada" id="dilaporkan_kepada" class="form-select" required>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Kepala Sekolah">Kepala Sekolah</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            `;
        const modal = new bootstrap.Modal(document.getElementById('modalCatatan'));
        modal.show();
    }

    // Modal untuk Lihat Catatan
    function openLihatCatatan() {
        document.getElementById('modalCatatanLabel').textContent = `Lihat Catatan`;
        document.getElementById('modalCatatanBody').innerHTML = `
                <ul class="list-group">
                    <?php
                    $result_siswa->data_seek(0); // Reset pointer
                    while ($siswa = $result_siswa->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($siswa['nama_siswa']); ?>
                        <a href="catatan_detail.php?id_siswa=<?= $siswa['id_siswa']; ?>&id_kelas=<?= $id_kelas; ?>" class="btn btn-info btn-sm">Lihat</a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            `;
        const modal = new bootstrap.Modal(document.getElementById('modalCatatan'));
        modal.show();
    }
    </script>

<?php include 'root/footer.php' ?>
</body>

</html>