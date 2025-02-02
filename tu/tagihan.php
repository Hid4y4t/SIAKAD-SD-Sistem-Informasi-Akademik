<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Mengambil data tagihan SPP dari tabel `tagihan_spp`
$queryTagihanSPP = "SELECT * FROM tagihan_spp ORDER BY created_at DESC";
$resultTagihanSPP = $mysqli->query($queryTagihanSPP);

if (!$resultTagihanSPP) {
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
                <h3>Tagihan SPP</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- Tabel Tagihan SPP -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Tagihan SPP</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTagihanModal">Tambah Tagihan Baru</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: scroll;">
                                        <table class="table table-bordered mb-0" id="tableTagihan">
                                            <thead>
                                                <tr>
                                                    <th>Angkatan</th>
                                                    <th>Jumlah Tagihan</th>
                                                    <th>Keterangan</th>
                                                    <th>Tanggal Dibuat</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultTagihanSPP->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['angkatan']); ?></td>
                                                    <td>Rp <?php echo number_format($row['jumlah_tagihan'], 0, ',', '.'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm btn-detail" data-id="<?php echo $row['id_tagihan']; ?>">Detail</button>
                                                        <button class="btn btn-warning btn-sm btn-edit" data-id="<?php echo $row['id_tagihan']; ?>">Edit</button>
                                                        <button class="btn btn-danger btn-sm btn-delete" data-id="<?php echo $row['id_tagihan']; ?>">Hapus</button>
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

    <!-- Modal Tambah Tagihan -->
    <div class="modal fade" id="addTagihanModal" tabindex="-1" aria-labelledby="addTagihanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="proses_tambah_tagihan.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTagihanModalLabel">Tambah Tagihan SPP Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="angkatan" class="form-control mb-3" placeholder="Angkatan" required>
                        <input type="number" name="jumlah_tagihan" class="form-control mb-3" placeholder="Jumlah Tagihan" required>
                        <textarea name="keterangan" class="form-control mb-3" placeholder="Keterangan"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Tagihan -->
    <div class="modal fade" id="editTagihanModal" tabindex="-1" aria-labelledby="editTagihanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditTagihan" action="proses_edit_tagihan.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTagihanModalLabel">Edit Tagihan SPP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_tagihan" name="id_tagihan">
                        <input type="text" id="edit_angkatan" name="angkatan" class="form-control mb-3" placeholder="Angkatan" required>
                        <input type="number" id="edit_jumlah_tagihan" name="jumlah_tagihan" class="form-control mb-3" placeholder="Jumlah Tagihan" required>
                        <textarea id="edit_keterangan" name="keterangan" class="form-control mb-3" placeholder="Keterangan"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Tagihan -->
    <div class="modal fade" id="detailTagihanModal" tabindex="-1" aria-labelledby="detailTagihanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailTagihanModalLabel">Detail Tagihan SPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Angkatan:</strong> <span id="detail_angkatan"></span></p>
                    <p><strong>Jumlah Tagihan:</strong> Rp <span id="detail_jumlah_tagihan"></span></p>
                    <p><strong>Keterangan:</strong> <span id="detail_keterangan"></span></p>
                    <p><strong>Tanggal Dibuat:</strong> <span id="detail_created_at"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'root/js.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript untuk Pencarian Real-time dan Aksi Modal -->
    <script>
        function searchTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("tableTagihan");
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

        // Edit dan Detail menggunakan AJAX
        $(document).on('click', '.btn-edit', function () {
    const id = $(this).data('id');
    console.log("Memulai proses edit untuk ID:", id); // Debugging
    $.get(`get_tagihan.php?id=${id}`, function (data) {
        try {
            const tagihan = JSON.parse(data);
            if (tagihan.error) {
                alert(tagihan.error); // Tampilkan pesan error jika ada
            } else {
                $('#edit_id_tagihan').val(tagihan.id_tagihan);
                $('#edit_angkatan').val(tagihan.angkatan);
                $('#edit_jumlah_tagihan').val(tagihan.jumlah_tagihan);
                $('#edit_keterangan').val(tagihan.keterangan);
                $('#editTagihanModal').modal('show');
            }
        } catch (error) {
            console.error("Gagal parsing JSON:", error, "Data yang diterima:", data);
        }
    });
});

$(document).on('click', '.btn-detail', function () {
    const id = $(this).data('id');
    console.log("Memulai proses detail untuk ID:", id); // Debugging
    $.get(`get_tagihan.php?id=${id}`, function (data) {
        try {
            const tagihan = JSON.parse(data);
            if (tagihan.error) {
                alert(tagihan.error); // Tampilkan pesan error jika ada
            } else {
                $('#detail_angkatan').text(tagihan.angkatan);
                $('#detail_jumlah_tagihan').text(new Intl.NumberFormat('id-ID').format(tagihan.jumlah_tagihan));
                $('#detail_keterangan').text(tagihan.keterangan);
                $('#detail_created_at').text(tagihan.created_at);
                $('#detailTagihanModal').modal('show');
            }
        } catch (error) {
            console.error("Gagal parsing JSON:", error, "Data yang diterima:", data);
        }
    });
});


        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus tagihan ini?")) {
                $.post('hapus_tagihan.php', { id_tagihan: id }, function () {
                    location.reload();
                });
            }
        });
    </script>
</body>
</html>

