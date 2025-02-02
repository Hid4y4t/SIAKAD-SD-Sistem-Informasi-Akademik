<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Mengambil data dari tabel `ppdb_nominal`
$queryNominal = "SELECT * FROM ppdb_nominal ORDER BY created_at DESC";
$resultNominal = $mysqli->query($queryNominal);

// Mengambil data dari tabel `ppdb_pembayaran` dengan batas 150 data
$queryPembayaran = "
    SELECT pp.*, s.nama_siswa, s.kelas 
    FROM ppdb_pembayaran pp 
    JOIN siswa s ON pp.id_siswa = s.id_siswa 
    LIMIT 150";
$resultPembayaran = $mysqli->query($queryPembayaran);

// Mengambil data dari tabel `siswa_bebas_ppdb`
$queryBebasPPDB = "
    SELECT sbp.*, s.nama_siswa, s.kelas 
    FROM siswa_bebas_ppdb sbp 
    JOIN siswa s ON sbp.id_siswa = s.id_siswa";
$resultBebasPPDB = $mysqli->query($queryBebasPPDB);
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
                <h3>Pengaturan PPDB</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- Tabel Nominal PPDB -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Nominal PPDB</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addNominalModal">Tambah Nominal</button>
                                </div>
                                <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
                                <div class="alert alert-success" role="alert" id="successAlert">
                                    Nominal PPDB berhasil ditambahkan.
                                </div>
                                <?php endif; ?>

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
                                                        <?php echo number_format($row['jumlah_total'], 0, ',', '.'); ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm btn-detail-nominal"
                                                            data-id="<?php echo $row['id_nominal']; ?>">Detail</button>

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

                        <!-- Tabel Siswa Bebas PPDB -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Siswa Bebas PPDB</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBebasModal">Tambah Siswa Bebas</button>
                                    <a href="export_siswa_bebas_ppdb.php" class="btn btn-success">Export ke Excel</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="tableBebasPPDB">
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
                                                <?php while ($row = $resultBebasPPDB->fetch_assoc()): ?>
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

                     <!-- Tabel Data Pembayaran PPDB -->
<section class="section">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Pembayaran PPDB</h4>
            <a href="export_ppdb.php" class="btn btn-success">Cetak ke Excel</a>
            <button class="btn btn-primary" id="addAllButton" data-bs-toggle="modal" data-bs-target="#selectAngkatanModal">Tambah Semua Data Siswa Berdasarkan Angkatan</button>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addPPDBModal">Tambah Data Siswa</button>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered mb-0" id="tablePPDB">
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
                        <?php while ($row = $resultPembayaran->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                            <td>Rp <?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['jumlah_terbayar'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="location.href='ppdb_history.php?id_siswa=<?php echo $row['id_siswa']; ?>'">Detail</button>
                                <button class="btn btn-danger btn-sm btn-delete-ppdb" data-id="<?php echo $row['id_pembayaran']; ?>">Hapus</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
                        <!-- Modal Detail Siswa Bebas PPDB -->
                        <div class="modal fade" id="detailBebasModal" tabindex="-1"
                            aria-labelledby="detailBebasModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailBebasModalLabel">Detail Siswa Bebas PPDB</h5>
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

                        <!-- Modal Tambah Nominal -->
                        <div class="modal fade" id="addNominalModal" tabindex="-1"
                            aria-labelledby="addNominalModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="proses_tambah_nominal_ppdb.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addNominalModalLabel">Tambah Nominal PPDB</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
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
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Detail Nominal PPDB -->
                        <div class="modal fade" id="detailNominalModal" tabindex="-1"
                            aria-labelledby="detailNominalModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailNominalModalLabel">Detail Nominal PPDB</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
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



                        <!-- Modal Tambah Siswa Bebas -->
                        <div class="modal fade" id="addBebasModal" tabindex="-1" aria-labelledby="addBebasModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="formTambahBebas" method="POST" action="proses_tambah_bebas_ppdb.php">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addBebasModalLabel">Tambah Siswa Bebas PPDB</h5>
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

                        <!-- Add PPDB Modal -->
                        <div class="modal fade" id="addPPDBModal" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="proses_tambah_ppdb.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tambah Data PPDB</h5>
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
                                                    $queryAngkatan = "SELECT id_nominal, angkatan, jumlah_total FROM ppdb_nominal";
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

                        <!-- Modal Pilih Angkatan untuk Tambah Semua Data -->
<div class="modal fade" id="selectAngkatanModal" tabindex="-1" aria-labelledby="selectAngkatanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addAllForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectAngkatanLabel">Pilih Angkatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="angkatanSelect">Pilih Angkatan:</label>
                    <select id="angkatanSelect" class="form-control" required>
                        <option value="">Pilih Angkatan</option>
                        <?php
                        $angkatanQuery = "SELECT DISTINCT angkatan FROM ppdb_nominal ORDER BY angkatan DESC";
                        $angkatanResult = $mysqli->query($angkatanQuery);
                        while ($row = $angkatanResult->fetch_assoc()) {
                            echo "<option value='{$row['angkatan']}'>{$row['angkatan']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Data</button>
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
    // Fungsi untuk menghilangkan notifikasi setelah 5 detik
    setTimeout(function() {
        const alertElement = document.getElementById('successAlert');
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 5000); // 5000 ms = 5 detik
    $(document).ready(function() {
        // Fungsi untuk Edit Nominal
        $(document).on('click', '.btn-edit-nominal', function() {
            const id_nominal = $(this).data('id');
            $.ajax({
                url: 'get_nominal_ppdb.php',
                type: 'GET',
                data: {
                    id_nominal: id_nominal
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#edit_id_nominal').val(data.id_nominal);
                        $('#edit_angkatan').val(data.angkatan);
                        $('#edit_jumlah_total').val(data.jumlah_total);
                        $('#editNominalModal').modal('show');
                    } else {
                        alert('Gagal mengambil data untuk edit.');
                    }
                }
            });
        });

        $(document).on('click', '.btn-detail-nominal', function() {
            const id_nominal = $(this).data('id');

            $.ajax({
                url: 'get_nominal_ppdb.php',
                type: 'GET',
                data: {
                    id_nominal: id_nominal
                },
                success: function(response) {
                    console.log("Response:",
                        response); // Tambahkan ini untuk melihat respons di konsol
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#detailModalAngkatan').text(data.angkatan);
                        $('#detailModalJumlahTotal').text(new Intl.NumberFormat('id-ID')
                            .format(data.jumlah_total));
                        $('#detailNominalModal').modal('show');
                    } else {
                        alert(data.error || 'Gagal mengambil data untuk detail.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error); // Debugging untuk error
                    alert("Terjadi kesalahan pada permintaan data.");
                }
            });
        });

        // Hapus Nominal
        $(document).on('click', '.btn-delete-nominal', function() {
            const id_nominal = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus nominal ini?")) {
                $.post('hapus_nominal_ppdb.php', {
                    id_nominal: id_nominal
                }, function(response) {
                    if (response.success) {
                        alert("Nominal berhasil dihapus.");
                        location.reload();
                    } else {
                        alert("Gagal menghapus nominal: " + response.message);
                    }
                }, 'json');
            }
        });
    });


    $(document).ready(function() {
        // Detail Siswa Bebas PPDB
        $(document).on('click', '.btn-detail-bebas', function() {
            const id_bebas = $(this).data('id');
            $.ajax({
                url: 'get_bebas_ppdb.php',
                type: 'GET',
                data: {
                    id_bebas: id_bebas
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (!data.error) {
                        $('#detail_nama_siswa').text(data.nama_siswa);
                        $('#detail_kelas').text(data.kelas);
                        $('#detail_alasan_bebas').text(data.alasan_bebas || "-");
                        $('#detail_tanggal_mulai').text(data.tanggal_mulai);
                        $('#detail_tanggal_selesai').text(data.tanggal_selesai ||
                            "Tidak ada");
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

        // Hapus Siswa Bebas PPDB
        let deleteId;
        $(document).on('click', '.btn-delete-bebas', function() {
            deleteId = $(this).data('id');
            $('#confirmDeleteModal').modal('show');
        });

        // Proses hapus setelah konfirmasi
        $('#confirmDeleteBtn').on('click', function() {
            $.ajax({
                url: 'hapus_bebas_ppdb.php',
                type: 'POST',
                data: {
                    id_bebas: deleteId
                },
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

    $(document).ready(function() {
        // Form tambah semua data berdasarkan angkatan
        $('#addAllForm').on('submit', function(e) {
            e.preventDefault();
            const selectedAngkatan = $('#angkatanSelect').val();
            if (!selectedAngkatan) {
                alert("Pilih angkatan terlebih dahulu.");
                return;
            }
            $.ajax({
                url: 'proses_tambah_semua_ppdb.php',
                type: 'POST',
                data: { angkatan: selectedAngkatan },
                success: function(response) {
                    alert(response.message || 'Data berhasil ditambahkan.');
                    location.reload();
                },
                error: function() {
                    alert('Terjadi kesalahan saat menambah data.');
                }
            });
        });

        // Hapus Data
        $(document).on('click', '.btn-delete-ppdb', function() {
            const idPembayaran = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.ajax({
                    url: 'hapus_ppdb.php',
                    type: 'POST',
                    data: { id_pembayaran: idPembayaran },
                    success: function(response) {
                        alert(response.message || 'Data berhasil dihapus.');
                        location.reload();
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghapus data.');
                    }
                });
            }
        });
    });
    </script>
</body>

</html>