<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Mengambil data dari tabel `buku`
$queryBuku = "SELECT * FROM buku ORDER BY kelas ASC";
$resultBuku = $mysqli->query($queryBuku);

// Mengambil data dari tabel `buku_pembayaran`
$queryPembayaran = "
    SELECT bp.*, s.nama_siswa, s.kelas, b.jenis_buku 
    FROM buku_pembayaran bp
    JOIN siswa s ON bp.id_siswa = s.id_siswa
    JOIN buku b ON bp.id_buku = b.id_buku
    ORDER BY bp.updated_at DESC";  // Urutkan berdasarkan kolom tanggal terbaru
$resultPembayaran = $mysqli->query($queryPembayaran);

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
                <h3>Pengaturan Buku</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">

                        <!-- Tabel Buku -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Buku</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBukuModal">Tambah Buku</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="tableBuku">
                                            <thead>
                                                <tr>
                                                    <th>Kelas</th>
                                                    <th>Jenis Buku</th>
                                                    <th>Harga</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultBuku->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['jenis_buku']); ?></td>
                                                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm btn-edit-buku"
                                                            data-id="<?php echo $row['id_buku']; ?>"
                                                            data-kelas="<?php echo $row['kelas']; ?>"
                                                            data-jenis_buku="<?php echo $row['jenis_buku']; ?>"
                                                            data-harga="<?php echo $row['harga']; ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editBukuModal">Edit</button>
                                                        <button class="btn btn-danger btn-sm btn-delete-buku"
                                                            data-id="<?php echo $row['id_buku']; ?>">Hapus</button>
                                                    </td>

                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                       <!-- Tabel Data Pembayaran Buku -->
<section class="section">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Pembayaran Buku</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPembayaranModal">Tambah Pembayaran</button>
            <button class="btn btn-warning" onclick="konfirmasiTambahSemua()">Tambah Semua</button>
            <button class="btn btn-danger" id="hapusTerpilihBtn">Hapus Terpilih</button>
            <a href="cetak_excel_pembayaran_buku.php" class="btn btn-success">Cetak ke Excel</a>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered mb-0" id="tablePembayaran">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Buku</th>
                            <th>Total Tagihan</th>
                            <th>Jumlah Terbayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultPembayaran->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" class="selectRow" value="<?php echo $row['id_pembayaran']; ?>"></td>
                            <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_buku']); ?></td>
                            <td>Rp <?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['jumlah_terbayar'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <a href="buku_history.php?id_siswa=<?php echo $row['id_siswa']; ?>" class="btn btn-info btn-sm">Detail</a>
                                
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

                        <!-- Modal Edit Buku -->
                        <div class="modal fade" id="editBukuModal" tabindex="-1" aria-labelledby="editBukuLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="proses_edit_buku.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editBukuLabel">Edit Buku</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_buku" id="edit-id-buku">
                                            <div class="mb-3">
                                                <label>Kelas</label>
                                                <input type="text" name="kelas" id="edit-kelas" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Jenis Buku</label>
                                                <select name="jenis_buku" id="edit-jenis-buku" class="form-control"
                                                    required>
                                                    <option value="ISMUBA">ISMUBA</option>
                                                    <option value="Pelajaran">Pelajaran</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label>Harga</label>
                                                <input type="number" name="harga" id="edit-harga" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Tambah Buku -->
                        <div class="modal fade" id="addBukuModal" tabindex="-1" aria-labelledby="addBukuLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="proses_tambah_buku.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addBukuLabel">Tambah Buku</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Kelas</label>
                                                <input type="text" name="kelas" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Jenis Buku</label>
                                                <select name="jenis_buku" class="form-control" required>
                                                    <option value="ISMUBA">ISMUBA</option>
                                                    <option value="Pelajaran">Pelajaran</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label>Harga</label>
                                                <input type="number" name="harga" class="form-control" required>
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

                        <!-- Modal Tambah Pembayaran Buku -->
                        <div class="modal fade" id="addPembayaranModal" tabindex="-1"
                            aria-labelledby="addPembayaranLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="proses_tambah_pembayaran_buku.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addPembayaranLabel">Tambah Pembayaran Buku</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>NIS Siswa</label>
                                                <input type="text" name="nis" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Buku</label>
                                                <select name="id_buku" class="form-control" required>
                                                    <option value="">Pilih Buku</option>
                                                    <?php
                                                    $resultBuku->data_seek(0); // Reset pointer
                                                    while ($row = $resultBuku->fetch_assoc()) {
                                                        echo "<option value='{$row['id_buku']}'>{$row['jenis_buku']} - Kelas {$row['kelas']} - Rp " . number_format($row['harga'], 0, ',', '.') . "</option>";
                                                    }
                                                    ?>
                                                </select>
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

                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include 'root/js.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Handle the delete button click for Buku
        $(document).on('click', '.btn-delete-pembayaran', function() {
            const idPembayaran = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus data pembayaran ini?")) {
                $.ajax({
                    url: 'hapus_pembayaran_buku.php',
                    type: 'POST',
                    data: {
                        id_pembayaran: idPembayaran
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload(); // Muat ulang halaman jika berhasil
                        } else {
                            alert("Gagal: " + response.message);
                        }
                    },
                    error: function() {
                        alert("Terjadi kesalahan pada koneksi.");
                    }
                });
            }
        });
        // Handle the delete button click for Pembayaran
        $(document).on('click', '.btn-delete-pembayaran', function() {
            const idPembayaran = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.ajax({
                    url: 'hapus_pembayaran_buku.php',
                    type: 'POST',
                    data: {
                        id_pembayaran: idPembayaran
                    },
                    success: function(response) {
                        if (response.success) {
                            alert("Data berhasil dihapus.");
                            location.reload();
                        } else {
                            alert("Gagal menghapus data: " + response.message);
                        }
                    },
                    error: function() {
                        alert("Terjadi kesalahan pada koneksi server.");
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        // Isi data pada modal edit buku
        $(document).on('click', '.btn-edit-buku', function() {
            const idBuku = $(this).data('id');
            const kelas = $(this).data('kelas');
            const jenisBuku = $(this).data('jenis_buku');
            const harga = $(this).data('harga');

            $('#edit-id-buku').val(idBuku);
            $('#edit-kelas').val(kelas);
            $('#edit-jenis-buku').val(jenisBuku);
            $('#edit-harga').val(harga);
        });
    });

    function konfirmasiTambahSemua() {
        if (confirm("Apakah Anda yakin ingin menambahkan data pembayaran untuk semua siswa berdasarkan kelasnya?")) {
            window.location.href = 'proses_tambah_semua_pembayaran.php';
        }
    }


    $(document).ready(function () {
    // Fungsi Pilih Semua
    $('#selectAll').on('click', function() {
        $('.selectRow').prop('checked', this.checked);
    });

    $('.selectRow').on('click', function() {
        if ($('.selectRow:checked').length === $('.selectRow').length) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
    });

    // Fungsi Hapus Terpilih
    $('#hapusTerpilihBtn').on('click', function () {
        const selectedIds = $('.selectRow:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert("Pilih setidaknya satu data untuk dihapus.");
            return;
        }

        if (confirm("Apakah Anda yakin ingin menghapus data terpilih?")) {
            $.ajax({
                url: 'hapus_pembayaran_buku.php',
                type: 'POST',
                data: { id_pembayaran: selectedIds },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert("Gagal: " + response.message);
                    }
                },
                error: function () {
                    alert("Terjadi kesalahan pada koneksi.");
                }
            });
        }
    });
});
    </script>

</body>

</html>