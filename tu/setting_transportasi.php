<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Mengambil data dari tabel `zona_transportasi`
$queryZona = "SELECT * FROM zona_transportasi ORDER BY nama_zona ASC";
$resultZona = $mysqli->query($queryZona);

// Mengambil data dari tabel `transportasi_pembayaran`
$queryPembayaran = "
    SELECT tp.*, s.nama_siswa, s.kelas, z.nama_zona 
    FROM transportasi_pembayaran tp
    JOIN siswa s ON tp.id_siswa = s.id_siswa
    JOIN zona_transportasi z ON tp.id_zona = z.id_zona";
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
                <h3>Pengaturan Transportasi</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">

                        <!-- Tabel Zona Transportasi -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Zona Transportasi</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addZonaModal">Tambah Zona</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="tableZona">
                                            <thead>
                                                <tr>
                                                    <th>Nama Zona</th>
                                                    <th>Harga Per Trip</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultZona->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nama_zona']); ?></td>
                                                    <td>Rp <?php echo number_format($row['harga_per_trip'], 0, ',', '.'); ?></td>
                                                    <td>
                                                       
                                                        <button class="btn btn-danger btn-sm btn-delete-zona" data-id="<?php echo $row['id_zona']; ?>">Hapus</button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Tabel Data Pembayaran Transportasi -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                           
                                    <h4 class="card-title">Data Pembayaran Transportasi</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSiswaModal">Tambah Siswa</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-bordered mb-0" id="tableTransportasi">
                                            <thead>
                                                <tr>
                                                    <th>Nama Siswa</th>
                                                    <th>Kelas</th>
                                                    <th>Zona</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultPembayaran->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_zona']); ?></td>
                                                    <td>
                                                    <button class="btn btn-danger btn-sm btn-delete-pembayaran" data-id="<?php echo $row['id_pembayaran']; ?>">Hapus</button>
                                                        
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Modal Tambah Zona Transportasi -->
                        <div class="modal fade" id="addZonaModal" tabindex="-1" aria-labelledby="addZonaLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="proses_tambah_zona.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addZonaLabel">Tambah Zona Transportasi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Nama Zona</label>
                                                <input type="text" name="nama_zona" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Harga Per Trip</label>
                                                <input type="number" name="harga_per_trip" class="form-control" required>
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

                        <!-- Modal Tambah Siswa -->
                        <div class="modal fade" id="addSiswaModal" tabindex="-1" aria-labelledby="addSiswaLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="proses_tambah_siswa_transportasi.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addSiswaLabel">Tambah Siswa Transportasi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>NIS Siswa</label>
                                                <input type="text" name="nis" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Zona Transportasi</label>
                                                <select name="id_zona" class="form-control" required>
                                                    <option value="">Pilih Zona</option>
                                                    <?php
                                                    $resultZona->data_seek(0); // Reset pointer
                                                    while ($row = $resultZona->fetch_assoc()) {
                                                        echo "<option value='{$row['id_zona']}'>{$row['nama_zona']} - Rp " . number_format($row['harga_per_trip'], 0, ',', '.') . "</option>";
                                                    }
                                                    ?>
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

                        <!-- Modal Konfirmasi Hapus -->
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus data ini?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                                    </div>
                                </div>
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
    // Handle the delete button click
    $(document).on('click', '.btn-delete-zona', function() {
        const id_zona = $(this).data('id');
        if (confirm("Apakah Anda yakin ingin menghapus zona ini?")) {
            $.ajax({
                url: 'hapus_zona.php', // PHP file to process deletion
                type: 'POST',
                data: { id_zona: id_zona },
                success: function(response) {
                    if (response.success) {
                        alert("Zona berhasil dihapus.");
                        location.reload();
                    } else {
                        alert("Gagal menghapus zona: " + response.message);
                    }
                },
                error: function() {
                    alert("Terjadi kesalahan pada koneksi.");
                }
            });
        }
    });
});
$(document).ready(function () {
    // Event listener untuk tombol hapus
    $(document).on('click', '.btn-delete-pembayaran', function () {
        const idPembayaran = $(this).data('id');
        if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
            $.ajax({
                url: 'hapus_pembayaran_transportasi.php', // Pastikan file PHP ini benar dan berada di lokasi yang sesuai
                type: 'POST',
                data: { id_pembayaran: idPembayaran },
                success: function (response) {
                    if (response.success) {
                        alert("Data berhasil dihapus.");
                        location.reload(); // Muat ulang halaman setelah penghapusan
                    } else {
                        alert(" menghapus data: " + (response.message || "."));
                    }
                },
                error: function () {
                    alert("Terjadi kesalahan pada koneksi server.");
                }
            });
        }
    });
});


</script>

</body>

</html>
