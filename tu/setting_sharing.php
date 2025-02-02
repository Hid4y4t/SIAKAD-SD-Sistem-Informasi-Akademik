<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Mengambil data dari tabel `dana_sharing_nominal`
$queryNominal = "SELECT * FROM dana_sharing_nominal ORDER BY created_at DESC";
$resultNominal = $mysqli->query($queryNominal);

// Mengambil data dari tabel `dana_sharing` dengan batas 20 data
$queryDanaSharing = "SELECT ds.*, s.nama_siswa, s.kelas 
                     FROM dana_sharing ds 
                     JOIN siswa s ON ds.id_siswa = s.id_siswa 
                     LIMIT 20";
$resultDanaSharing = $mysqli->query($queryDanaSharing);







// Mengambil data dari tabel `siswa_bebas_dana_sharing`
$queryBebasDanaSharing = "SELECT bds.*, s.nama_siswa, s.kelas 
                          FROM siswa_bebas_dana_sharing bds 
                          JOIN siswa s ON bds.id_siswa = s.id_siswa";
$resultBebasDanaSharing = $mysqli->query($queryBebasDanaSharing);
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
                <h3>Pengaturan Dana Sharing</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">

                        <!-- Tabel Nominal Dana Sharing -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Nominal Dana Sharing</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addNominalModal">Tambah Nominal</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="tableNominal">
                                            <thead>
                                                <tr>
                                                    <th>Kelas</th>
                                                    <th>Angkatan</th>
                                                    <th>Semester</th>
                                                    <th>Jumlah Tagihan</th>
                                                    <th>Keterangan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultNominal->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['angkatan']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                                    <td>Rp
                                                        <?php echo number_format($row['jumlah_tagihan'], 0, ',', '.'); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm btn-detail-nominal"
                                                            data-id="<?php echo $row['id_nominal']; ?>">Detail</button>
                                                        <button class="btn btn-warning btn-sm btn-edit-nominal"
                                                            data-id="<?php echo $row['id_nominal']; ?>">Edit</button>
                                                        <button class="btn btn-danger btn-sm btn-delete-nominal"
                                                            data-id="<?php echo $row['id_nominal']; ?>">Hapus</button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                         <!-- Tabel Data Dana Sharing -->
                         <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Dana Sharing</h4>
                                    <button class="btn btn-primary" id="btnMasukkanSemuaData">Masukkan Semua Data</button>
                                    <a href="export_dana_sharing.php" class="btn btn-success">Cetak ke Excel</a>
                                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addDanaSharingModal">Tambah Data Siswa</button>
                                </div>
                                <div class="card-body">
                                    <!-- Search Bar for Data Dana Sharing -->
                                    <div class="input-group mb-3">
                                        <input type="text" id="searchDanaSharing" class="form-control" placeholder="Cari data di Dana Sharing...">
                                    </div>
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-bordered mb-0" id="tableDanaSharing">
                                            <thead>
                                                <tr>
                                                    <th>Nama Siswa</th>
                                                    <th>Kelas</th>
                                                    <th>Tanggal Pembayaran</th>
                                                    <th>Jumlah Tagihan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultDanaSharing->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo $row['tanggal_pembayaran'] ? date('d-m-Y', strtotime($row['tanggal_pembayaran'])) : 'Belum ada tanggal'; ?></td>
                                                    <td>Rp <?php echo number_format($row['jumlah_tagihan'], 0, ',', '.'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm" onclick="location.href='dana_sharing_history.php?id_siswa=<?php echo $row['id_siswa']; ?>'">Detail</button>
                                                        <button class="btn btn-danger btn-sm btn-delete-sharing" data-id="<?php echo $row['id_dana_sharing']; ?>">Hapus</button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Tabel Siswa Bebas Dana Sharing -->
                        <!-- Tabel Siswa Bebas Dana Sharing -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Siswa Bebas Dana Sharing</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBebasModal">Tambah Siswa Bebas</button>
                                    <a href="export_siswa_bebas_dana_sharing.php" class="btn btn-success">Export ke
                                        Excel</a>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="tableBebasDanaSharing">
                                            <thead>
                                                <tr>
                                                    <th>Nama Siswa</th>
                                                    <th>Kelas</th>
                                                    <th>Alasan Bebas</th>
                                                    <th>Tanggal Mulai</th>
                                                    <th>Tanggal Selesai</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultBebasDanaSharing->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['alasan_bebas']); ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($row['tanggal_mulai'])); ?>
                                                    </td>
                                                    <td><?php echo $row['tanggal_selesai'] ? date('d-m-Y', strtotime($row['tanggal_selesai'])) : 'Tidak ada'; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm btn-detail-bebas"
                                                            data-id="<?php echo $row['id_bebas']; ?>">Detail</button>
                                                        <button class="btn btn-danger btn-sm btn-delete-bebas"
                                                            data-id="<?php echo $row['id_bebas']; ?>">Hapus</button>
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

    <!-- Modals -->


    <!-- Modal Tambah Data Siswa -->
    <div class="modal fade" id="addDanaSharingModal" tabindex="-1" aria-labelledby="addDanaSharingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="proses_tambah_dana_sharing_siswa.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDanaSharingModalLabel">Tambah Data Siswa Dana Sharing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nis_siswa" class="form-label">NIS Siswa</label>
                            <input type="text" name="nis_siswa" id="nis_siswa" class="form-control"
                                placeholder="Masukkan NIS Siswa" required>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan</label>
                            <input type="number" name="jumlah_tagihan" class="form-control"
                                placeholder="Masukkan jumlah tagihan" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Belum Lunas">Belum Lunas</option>
                                <option value="Lunas">Lunas</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Tambah Siswa Bebas Dana Sharing -->
    <div class="modal fade" id="addBebasModal" tabindex="-1" aria-labelledby="addBebasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambahBebas" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBebasModalLabel">Tambah Siswa Bebas Dana Sharing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="nis" name="nis" class="form-control mb-3"
                            placeholder="Masukkan NIS Siswa" required>
                        <input type="text" name="alasan_bebas" class="form-control mb-3" placeholder="Alasan Bebas"
                            required>
                        <input type="date" name="tanggal_mulai" class="form-control mb-3" required>
                        <input type="date" name="tanggal_selesai" class="form-control mb-3">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="submitTambahBebas">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Detail Siswa Bebas Dana Sharing -->
    <!-- Modal Detail Siswa Bebas Dana Sharing -->
    <div class="modal fade" id="detailBebasModal" tabindex="-1" aria-labelledby="detailBebasModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailBebasModalLabel">Detail Siswa Bebas Dana Sharing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama Siswa:</strong> <span id="detail_nama_siswa"></span></p>
                    <p><strong>Kelas:</strong> <span id="detail_kelas"></span></p>
                    <p><strong>Alasan Bebas:</strong> <span id="detail_alasan_bebas"></span></p>
                    <p><strong>Tanggal Mulai:</strong> <span id="detail_tanggal_mulai"></span></p>
                    <p><strong>Tanggal Selesai:</strong> <span id="detail_tanggal_selesai"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Dana Sharing -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data dana sharing ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Tambah Nominal Dana Sharing -->
    <div class="modal fade" id="addNominalModal" tabindex="-1" aria-labelledby="addNominalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="proses_tambah_nominal.php" method="POST" id="formTambahNominal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNominalModalLabel">Tambah Nominal Dana Sharing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form input -->
                        <div class="mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="angkatan" class="form-label">Angkatan</label>
                            <input type="text" name="angkatan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select name="semester" class="form-control" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan</label>
                            <input type="number" name="jumlah_tagihan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Nominal Dana Sharing -->
    <div class="modal fade" id="editNominalModal" tabindex="-1" aria-labelledby="editNominalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="proses_edit_nominal.php" method="POST" id="formEditNominal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNominalModalLabel">Edit Nominal Dana Sharing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form input -->
                        <input type="hidden" name="id_nominal" id="edit_id_nominal">
                        <div class="mb-3">
                            <label for="edit_kelas" class="form-label">Kelas</label>
                            <input type="text" name="kelas" id="edit_kelas" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_angkatan" class="form-label">Angkatan</label>
                            <input type="text" name="angkatan" id="edit_angkatan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_semester" class="form-label">Semester</label>
                            <select name="semester" id="edit_semester" class="form-control" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jumlah_tagihan" class="form-label">Jumlah Tagihan</label>
                            <input type="number" name="jumlah_tagihan" id="edit_jumlah_tagihan" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>

                            <input type="text" name="keterangan" id="edit_keterangan" class="form-control" required>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Nominal Dana Sharing -->
    <div class="modal fade" id="detailNominalModal" tabindex="-1" aria-labelledby="detailNominalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Konten modal detail -->
                <div class="modal-header">
                    <h5 class="modal-title" id="detailNominalModalLabel">Detail Nominal Dana Sharing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tampilkan detail -->
                    <p><strong>Kelas:</strong> <span id="detail_kelas"></span></p>
                    <p><strong>Angkatan:</strong> <span id="detail_angkatan"></span></p>
                    <p><strong>Semester:</strong> <span id="detail_semester"></span></p>
                    <p><strong>Jumlah Tagihan:</strong> Rp <span id="detail_jumlah_tagihan"></span></p>
                    <p><strong>Keterangan:</strong> <span id="detail_keterangan"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Dana Sharing -->
    <div class="modal fade" id="detailDanaSharingModal" tabindex="-1" aria-labelledby="detailDanaSharingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Konten modal detail -->
                <div class="modal-header">
                    <h5 class="modal-title" id="detailDanaSharingModalLabel">Detail Dana Sharing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tampilkan detail -->
                    <p><strong>Nama Siswa:</strong> <span id="detail_nama_siswa"></span></p>
                    <p><strong>Kelas:</strong> <span id="detail_kelas_siswa"></span></p>
                    <p><strong>Jumlah Tagihan:</strong> Rp <span id="detail_jumlah_tagihan_siswa"></span></p>
                    <p><strong>Status:</strong> <span id="detail_status_siswa"></span></p>
                    <p><strong>Tanggal Pembayaran:</strong> <span id="detail_tanggal_pembayaran_siswa"></span></p>
                    <!-- Tambahkan detail lain jika diperlukan -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Modal dan AJAX -->
    <?php include 'root/js.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {

        // Handle Edit Nominal
        $(document).on('click', '.btn-edit-nominal', function() {
            const id_nominal = $(this).data('id');
            $.get('get_nominal.php', {
                id: id_nominal
            }, function(data) {
                const response = JSON.parse(data);
                if (!response.error) {
                    $('#edit_id_nominal').val(response.id_nominal);
                    $('#edit_kelas').val(response.kelas);
                    $('#edit_angkatan').val(response.angkatan);
                    $('#edit_semester').val(response.semester);
                    $('#edit_jumlah_tagihan').val(response.jumlah_tagihan);
                    $('#edit_keterangan').val(response.keterangan);
                    $('#editNominalModal').modal('show');
                } else {
                    alert(response.error);
                }
            });
        });

        // Handle Detail Nominal
        $(document).on('click', '.btn-detail-nominal', function() {
            const id_nominal = $(this).data('id');
            $.get('get_nominal.php', {
                id: id_nominal
            }, function(data) {
                const response = JSON.parse(data);
                if (!response.error) {
                    $('#detail_kelas').text(response.kelas);
                    $('#detail_angkatan').text(response.angkatan);
                    $('#detail_semester').text(response.semester);
                    $('#detail_jumlah_tagihan').text(new Intl.NumberFormat('id-ID').format(
                        response.jumlah_tagihan));
                    $('#detail_keterangan').text(response.keterangan || "-");
                    $('#detailNominalModal').modal('show');
                } else {
                    alert(response.error);
                }
            });
        });

        // Handle Delete Nominal
        $(document).on('click', '.btn-delete-nominal', function() {
            const id_nominal = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.post('hapus_nominal.php', {
                    id_nominal: id_nominal
                }, function() {
                    location.reload();
                });
            }
        });

        // Handle Detail Dana Sharing
        $(document).on('click', '.btn-detail-dana-sharing', function() {
            const id_dana_sharing = $(this).data('id');
            $.get('get_dana_sharing.php', {
                id: id_dana_sharing
            }, function(data) {
                const response = JSON.parse(data);
                if (!response.error) {
                    $('#detail_nama_siswa').text(response.nama_siswa);
                    $('#detail_kelas_siswa').text(response.kelas);
                    $('#detail_jumlah_tagihan_siswa').text(new Intl.NumberFormat('id-ID')
                        .format(response.jumlah_tagihan));
                    $('#detail_status_siswa').text(response.status);
                    $('#detail_tanggal_pembayaran_siswa').text(response.tanggal_pembayaran ?
                        new Date(response.tanggal_pembayaran).toLocaleDateString('id-ID') :
                        'Belum ada tanggal');
                    $('#detailDanaSharingModal').modal('show');
                } else {
                    alert(response.error);
                }
            });
        });

        // Handle Delete Dana Sharing
        $(document).on('click', '.btn-delete-dana-sharing', function() {
            const id_dana_sharing = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.post('hapus_dana_sharing.php', {
                    id_dana_sharing: id_dana_sharing
                }, function() {
                    location.reload();
                });
            }
        });

        // Handle Masukkan Semua Data
        $('#btnMasukkanSemuaData').on('click', function() {
            if (confirm("Apakah Anda yakin ingin memasukkan semua data?")) {
                $.post('proses_tambah_semua_dana_sharing.php', function(response) {
                    alert(response.message);
                    location.reload();
                }, 'json');
            }
        });
    });



    let deleteId; // Variabel untuk menyimpan id_dana_sharing yang akan dihapus

    $(document).on('click', '.btn-delete-sharing', function() {
        deleteId = $(this).data('id'); // Ambil id_dana_sharing
        $('#confirmDeleteModal').modal('show'); // Tampilkan modal konfirmasi
    });

    // Proses penghapusan setelah konfirmasi
    $('#confirmDeleteBtn').on('click', function() {
        $.ajax({
            url: 'hapus_dana_sharing.php',
            type: 'POST',
            data: {
                id_dana_sharing: deleteId
            },
            success: function(response) {
                if (response === 'success') {
                    $('#confirmDeleteModal').modal('hide');
                    location.reload(); // Muat ulang halaman setelah berhasil menghapus
                } else {
                    alert('Gagal menghapus data.');
                }
            },
            error: function() {
                alert('Terjadi kesalahan pada koneksi.');
            }
        });
    });

    function exportToExcel() {
        window.location.href = 'export_dana_sharing.php';
    }

    $(document).ready(function() {
        $('#submitTambahBebas').click(function() {
            const nis = $('#nis').val().trim();
            const alasanBebas = $('input[name="alasan_bebas"]').val().trim();
            const tanggalMulai = $('input[name="tanggal_mulai"]').val();
            const tanggalSelesai = $('input[name="tanggal_selesai"]').val();

            if (!nis || !alasanBebas || !tanggalMulai) {
                alert('Harap lengkapi semua bidang yang wajib diisi.');
                return;
            }

            $.ajax({
                url: 'proses_tambah_bebas_dana_sharing.php',
                type: 'POST',
                data: {
                    nis: nis,
                    alasan_bebas: alasanBebas,
                    tanggal_mulai: tanggalMulai,
                    tanggal_selesai: tanggalSelesai
                },
                success: function(response) {
                    if (response === 'success') {
                        alert('Data berhasil ditambahkan.');
                        $('#addBebasModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response); // Menampilkan pesan error jika NIS tidak ditemukan
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menambahkan data.');
                }
            });
        });


        // Detail Siswa Bebas Dana Sharing
        $(document).on('click', '.btn-detail-bebas', function() {
            const id_bebas = $(this).data('id');

            $.ajax({
                url: 'get_bebas_dana_sharing.php',
                type: 'GET',
                data: {
                    id_bebas: id_bebas
                },
                success: function(response) {
                    const data = JSON.parse(response);

                    if (data.error) {
                        alert(data.error);
                    } else {
                        $('#detail_nama_siswa').text(data.nama_siswa);
                        $('#detail_kelas').text(data.kelas);
                        $('#detail_alasan_bebas').text(data.alasan_bebas || "-");
                        $('#detail_tanggal_mulai').text(data.tanggal_mulai);
                        $('#detail_tanggal_selesai').text(data.tanggal_selesai ||
                            "Tidak ada");
                        $('#detailBebasModal').modal('show');
                    }
                },
                error: function() {
                    alert("Terjadi kesalahan saat mengambil data.");
                }
            });
        });

        // Hapus Siswa Bebas Dana Sharing
        $(document).on('click', '.btn-delete-bebas', function() {
            const id_bebas = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.ajax({
                    url: 'hapus_bebas_dana_sharing.php',
                    type: 'POST',
                    data: {
                        id_bebas: id_bebas
                    },
                    success: function(response) {
                        if (response === 'success') {
                            alert("Data berhasil dihapus.");
                            location.reload();
                        } else {
                            alert(response);
                        }
                    },
                    error: function() {
                        alert("Terjadi kesalahan saat menghapus data.");
                    }
                });
            }
        });
    });



    // Search functionality for Data Dana Sharing table
    document.getElementById("searchDanaSharing").addEventListener("keyup", function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll("#tableDanaSharing tbody tr");

            rows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(filter) ? "" : "none";
            });
        });
    </script>
</body>

</html>