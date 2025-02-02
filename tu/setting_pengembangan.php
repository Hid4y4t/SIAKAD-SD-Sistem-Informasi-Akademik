<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Mengambil data dari tabel `dana_pengembangan_nominal`
$queryNominal = "SELECT * FROM dana_pengembangan_nominal ORDER BY created_at DESC";
$resultNominal = $mysqli->query($queryNominal);

// Mengambil data dari tabel `dana_pengembangan` dengan batas 20 data
$queryDanaPengembangan = "
    SELECT dp.*, s.nama_siswa, s.kelas 
    FROM dana_pengembangan dp 
    JOIN siswa s ON dp.id_siswa = s.id_siswa 
    LIMIT 150";
$resultDanaPengembangan = $mysqli->query($queryDanaPengembangan);

// Mengambil data dari tabel `siswa_bebas_dana_pengembangan`
$queryBebasDanaPengembangan = "
    SELECT bdp.*, s.nama_siswa, s.kelas 
    FROM siswa_bebas_dana_pengembangan bdp 
    JOIN siswa s ON bdp.id_siswa = s.id_siswa";
$resultBebasDanaPengembangan = $mysqli->query($queryBebasDanaPengembangan);
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
                <h3>Pengaturan Dana Pengembangan</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">

                        <!-- Tabel Nominal Dana Pengembangan -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Nominal Dana Pengembangan</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addNominalModal">Tambah Nominal</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="tableNominal">
                                            <thead>
                                                <tr>
                                                    <th>Angkatan</th>
                                                    <th>Jumlah Tagihan</th>

                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultNominal->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['angkatan']); ?></td>
                                                    <td>Rp
                                                        <?php echo isset($row['jumlah_total']) ? number_format($row['jumlah_total'], 0, ',', '.') : '0'; ?>
                                                    </td>


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
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Siswa Bebas Dana Pengembangan</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBebasModal">Tambah Siswa Bebas</button>
                                    <a href="export_siswa_bebas_dana_pengembangan.php" class="btn btn-success">Export ke
                                        Excel</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="tableBebasDanaPengembangan">
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
                                                <?php while ($row = $resultBebasDanaPengembangan->fetch_assoc()): ?>
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

                        <!-- Tabel Data Dana Pengembangan -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Dana Pengembangan</h4>
                                    <a href="export_dana_pengembangan.php" class="btn btn-success">Cetak ke Excel</a>

                                    <button class="btn btn-primary" onclick="processAllData()">Masukkan Semua
                                        Data</button>
                                    <button class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#addDanaPengembanganModal">Tambah Data Siswa</button>
                                </div>
                                <div class="card-body">
                                    <div class="input-group mb-3">
                                        <input type="text" id="searchDanaPengembangan" class="form-control"
                                            placeholder="Cari data di Dana Pengembangan...">
                                    </div>
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-bordered mb-0" id="tableDanaPengembangan">
                                            <thead>
                                                <tr>
                                                    <th>Nama Siswa</th>
                                                    <th>Kelas</th>
                                                    <th>Jumlah Tagihan</th>
                                                    <th>Jumlah Terbayar</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultDanaPengembangan->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td>Rp
                                                        <?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?>
                                                    </td>
                                                    <td>Rp
                                                        <?php echo number_format($row['jumlah_terbayar'], 0, ',', '.'); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm"
                                                            onclick="location.href='pengembangan_history.php?id_siswa=<?php echo $row['id_siswa']; ?>'">Detail</button>
                                                        <button class="btn btn-danger btn-sm btn-delete-pengembangan"
                                                            data-id="<?php echo $row['id_dana_pengembangan']; ?>">Hapus</button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                        

                        <!-- Modal Tambah Siswa Bebas -->
                        <div class="modal fade" id="addBebasModal" tabindex="-1" aria-labelledby="addBebasModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="formTambahBebas" method="POST" action="proses_tambah_bebas.php">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addBebasModalLabel">Tambah Siswa Bebas Dana
                                                Pengembangan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="text" name="nis" class="form-control mb-3"
                                                placeholder="Masukkan NIS Siswa" required>
                                            <input type="text" name="alasan_bebas" class="form-control mb-3"
                                                placeholder="Alasan Bebas" required>
                                            <input type="date" name="tanggal_mulai" class="form-control mb-3" required>
                                            <input type="date" name="tanggal_selesai" class="form-control mb-3">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Detail Siswa Bebas -->
                        <div class="modal fade" id="detailBebasModal" tabindex="-1"
                            aria-labelledby="detailBebasModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailBebasModalLabel">Detail Siswa Bebas Dana
                                            Pengembangan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Nama Siswa:</strong> <span id="detail_nama_siswa"></span></p>
                                        <p><strong>Kelas:</strong> <span id="detail_kelas"></span></p>
                                        <p><strong>Alasan Bebas:</strong> <span id="detail_alasan_bebas"></span></p>
                                        <p><strong>Tanggal Mulai:</strong> <span id="detail_tanggal_mulai"></span></p>
                                        <p><strong>Tanggal Selesai:</strong> <span id="detail_tanggal_selesai"></span>
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Konfirmasi Hapus -->
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1"
                            aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus data ini?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-danger"
                                            id="confirmDeleteBtn">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Add Data Modal -->
                        <div class="modal fade" id="addDanaPengembanganModal" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="proses_tambah_dana_pengembangan.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tambah Data Dana Pengembangan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>NIS Siswa</label>
                                                <input type="text" name="nis_siswa" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Angkatan</label>
                                                <select name="id_nominal" class="form-control" required>
                                                    <option value="">Pilih Angkatan</option>
                                                    <?php
                    // Query untuk mendapatkan daftar angkatan dari tabel dana_pengembangan_nominal
                    $queryAngkatan = "SELECT id_nominal, angkatan, jumlah_total FROM dana_pengembangan_nominal";
                    $resultAngkatan = $mysqli->query($queryAngkatan);

                    while ($row = $resultAngkatan->fetch_assoc()) {
                        echo "<option value='{$row['id_nominal']}'>Angkatan {$row['angkatan']} - Rp " . number_format($row['jumlah_total'], 0, ',', '.') . "</option>";
                    }
                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>

                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailDanaPengembanganModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Dana Pengembangan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Nama Siswa:</strong> <span id="detail_nama_siswa"></span></p>
                                        <p><strong>Kelas:</strong> <span id="detail_kelas"></span></p>
                                        <p><strong>Jumlah Tagihan:</strong> Rp <span id="detail_total_tagihan"></span>
                                        </p>
                                        <p><strong>Jumlah Terbayar:</strong> Rp <span
                                                id="detail_jumlah_terbayar"></span></p>
                                        <p><strong>Status:</strong> <span id="detail_status"></span></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Add Nominal Modal -->
                        <div class="modal fade" id="addNominalModal" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="proses_tambah_nominal_p.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tambah Nominal Dana Pengembangan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Angkatan</label>
                                                <input type="text" name="angkatan" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Jumlah Total</label>
                                                <input type="number" name="jumlah_total" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Detail Nominal Modal -->
                        <div class="modal fade" id="detailNominalModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Nominal Dana Pengembangan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Angkatan:</strong> <span id="detailModalAngkatan"></span></p>
                                        <p><strong>Jumlah Total:</strong> Rp <span id="detailModalJumlahTotal"></span>
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Nominal Modal -->
                        <div class="modal fade" id="editNominalModal" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="proses_edit_nominal_p.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Nominal Dana Pengembangan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_nominal" id="edit_id_nominal">
                                            <div class="mb-3">
                                                <label>Angkatan</label>
                                                <input type="text" name="angkatan" id="edit_angkatan"
                                                    class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Jumlah Total</label>
                                                <input type="number" name="jumlah_total" id="edit_jumlah_total"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include 'root/js.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Search functionality for Data Dana Pengembangan table
        $("#searchDanaPengembangan").on("keyup", function() {
            const value = $(this).val().toLowerCase();
            $("#tableDanaPengembangan tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });

    $(document).ready(function() {
        // Add Nominal (triggered when the "Tambah Nominal" button is clicked)
        $('#addNominalModal form').submit(function(e) {
            e.preventDefault();
            $.post('proses_tambah_nominal_p.php', $(this).serialize(), function(response) {
                alert(response.message);
                if (response.success) location.reload();
            }, 'json');
        });

        // Detail Nominal
        $(document).on('click', '.btn-detail-nominal', function() {
            const id_nominal = $(this).data('id');
            $.get('get_nominal_p.php', {
                id: id_nominal
            }, function(data) {
                const response = JSON.parse(data);
                if (!response.error) {
                    $('#detailModalAngkatan').text(response.angkatan);
                    $('#detailModalJumlahTotal').text(new Intl.NumberFormat('id-ID').format(
                        response.jumlah_total));
                    $('#detailNominalModal').modal('show');
                } else {
                    alert(response.error);
                }
            });
        });

        // Edit Nominal
        $(document).on('click', '.btn-edit-nominal', function() {
            const id_nominal = $(this).data('id');
            $.get('get_nominal_p.php', {
                id: id_nominal
            }, function(data) {
                const response = JSON.parse(data);
                if (!response.error) {
                    $('#edit_id_nominal').val(response.id_nominal);
                    $('#edit_angkatan').val(response.angkatan);
                    $('#edit_jumlah_total').val(response.jumlah_total);
                    $('#editNominalModal').modal('show');
                } else {
                    alert(response.error);
                }
            });
        });

        // Delete Nominal
        $(document).on('click', '.btn-delete-nominal', function() {
            const id_nominal = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus nominal ini?")) {
                $.post('hapus_nominal_p.php', {
                    id_nominal: id_nominal
                }, function(response) {
                    alert(response.message);
                    if (response.success) location.reload();
                }, 'json');
            }
        });
    });
    $(document).ready(function() {
        // Search in Data Dana Pengembangan
        $("#searchDanaPengembangan").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tableDanaPengembangan tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Delete function for Dana Pengembangan
        $(document).on('click', '.btn-delete-pengembangan', function() {
            var id_pengembangan = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.post('hapus_dana_pengembangan.php', {
                    id_dana_pengembangan: id_pengembangan
                }, function(response) {
                    location.reload();
                });
            }
        });

        // Insert all data for students by angkatan
        $('#addAllDataBtn').on('click', function() {
            if (confirm("Masukkan semua data secara otomatis berdasarkan angkatan?")) {
                $.post('proses_tambah_semua_pengembangan.php', function(response) {
                    alert(response.message || 'Data berhasil ditambahkan.');
                    location.reload();
                });
            }
        });
    });

    function processAllData() {
        if (confirm("Apakah Anda yakin ingin memasukkan semua data secara otomatis?")) {
            fetch('proses_tambah_semua_dana_pengembangan.php')
                .then(response => response.text())
                .then(data => alert(data))
                .catch(error => alert("Terjadi kesalahan: " + error));
        }
    }


    $(document).ready(function() {
    // Handle Detail Modal
    $(document).on('click', '.btn-detail-bebas', function() {
        const id_bebas = $(this).data('id');
        $.ajax({
            url: 'get_bebas_dana_pengembangan.php',
            type: 'GET',
            data: { id_bebas: id_bebas },
            success: function(response) {
                const data = JSON.parse(response);
                if (!data.error) {
                    $('#detail_nama_siswa').text(data.nama_siswa);
                    $('#detail_kelas').text(data.kelas);
                    $('#detail_alasan_bebas').text(data.alasan_bebas || "-");
                    $('#detail_tanggal_mulai').text(data.tanggal_mulai);
                    $('#detail_tanggal_selesai').text(data.tanggal_selesai || "Tidak ada");
                    $('#detailBebasModal').modal('show');
                } else {
                    alert(data.error);
                }
            },
            error: function() {
                alert("Terjadi kesalahan saat mengambil data.");
            }
        });
    });

    // Handle Hapus Modal
    let deleteId;
    $(document).on('click', '.btn-delete-bebas', function() {
        deleteId = $(this).data('id');
        $('#confirmDeleteModal').modal('show');
    });

    // Proses hapus setelah konfirmasi
    $('#confirmDeleteBtn').on('click', function() {
        $.ajax({
            url: 'hapus_bebas_dana_pengembangan.php',
            type: 'POST',
            data: { id_bebas: deleteId },
            success: function(response) {
                if (response === 'success') {
                    $('#confirmDeleteModal').modal('hide');
                    location.reload();
                } else {
                    alert(response);
                }
            },
            error: function() {
                alert("Terjadi kesalahan pada koneksi.");
            }
        });
    });
});
    </script>
</body>

</html>