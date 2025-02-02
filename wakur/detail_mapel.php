<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID kelas dari URL
if (!isset($_GET['id_kelas'])) {
    header("Location: mapel.php");
    exit;
}
$id_kelas = $_GET['id_kelas'];

// Ambil data kelas
$query_kelas = "SELECT nama_kelas FROM kelas WHERE id_kelas = ?";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_kelas);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();
$kelas = $result_kelas->fetch_assoc();

if (!$kelas) {
    echo "Kelas tidak ditemukan.";
    exit;
}
$query_pelajaran = "
    SELECT p.id_pelajaran, p.nama_pelajaran, g.nama_guru 
    FROM pelajaran p
    JOIN guru g ON p.id_guru = g.id_guru
    WHERE p.id_kelas = ?
";
$stmt_pelajaran = $mysqli->prepare($query_pelajaran);
$stmt_pelajaran->bind_param("i", $id_kelas);
$stmt_pelajaran->execute();
$result_pelajaran = $stmt_pelajaran->get_result();


// Ambil daftar guru
$query_guru = "SELECT id_guru, nama_guru FROM guru ORDER BY nama_guru ASC";
$result_guru = $mysqli->query($query_guru);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<body>
    <div id="app">
        <?php include 'root/menu.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Detail Kelas - <?= htmlspecialchars($kelas['nama_kelas']); ?></h3>
            </div>

            <div class="page-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Data Pelajaran</h4>
                                <button class="btn btn-primary btn-sm" onclick="openTambahModal()">Tambah Pelajaran</button>
                            </div>
                            <div class="card-body">
                            <table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pelajaran</th>
            <th>Nama Guru</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php while ($pelajaran = $result_pelajaran->fetch_assoc()): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($pelajaran['nama_pelajaran']); ?></td>
            <td><?= htmlspecialchars($pelajaran['nama_guru']); ?></td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= $pelajaran['id_pelajaran']; ?>)">
                    Edit
                </button>
                <a href="hapus_pelajaran.php?id_pelajaran=<?= $pelajaran['id_pelajaran']; ?>&id_kelas=<?= $id_kelas; ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin ingin menghapus pelajaran ini?')">
                    Hapus
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalEditPelajaran" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPelajaran" method="POST" action="edit_pelajaran.php">
                <div class="modal-body">
                    <input type="hidden" name="id_pelajaran" id="editIdPelajaran">
                    <div class="mb-3">
                        <label for="editNamaPelajaran" class="form-label">Nama Pelajaran</label>
                        <input type="text" class="form-control" id="editNamaPelajaran" name="nama_pelajaran" required>
                    </div>
                    <div class="mb-3">
                        <label for="editIdGuru" class="form-label">Guru Pengampu</label>
                        <select class="form-select" id="editIdGuru" name="id_guru" required>
                            <!-- Options akan diisi oleh JavaScript -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

            <!-- Modal Tambah -->
            <div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Pelajaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="proses_tambah_pelajaran.php">
                            <div class="modal-body">
                                <input type="hidden" name="id_kelas" value="<?= $id_kelas; ?>">
                                <div class="mb-3">
                                    <label for="nama_pelajaran" class="form-label">Nama Pelajaran</label>
                                    <input type="text" name="nama_pelajaran" id="nama_pelajaran" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="id_guru" class="form-label">Nama Guru</label>
                                    <select name="id_guru" id="id_guru" class="form-select" required>
                                        <option value="" disabled selected>Pilih Guru</option>
                                        <?php while ($guru = $result_guru->fetch_assoc()): ?>
                                        <option value="<?= $guru['id_guru']; ?>"><?= htmlspecialchars($guru['nama_guru']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php include 'root/footer.php'; ?>
            <?php include 'root/js.php'; ?>
        </div>
    </div>

    <script>
        function openTambahModal() {
            const modal = new bootstrap.Modal(document.getElementById('tambahModal'));
            modal.show();
        }

        function openEditModal(idPelajaran) {
    // Ambil data pelajaran berdasarkan idPelajaran
    fetch(`get_pelajaran.php?id_pelajaran=${idPelajaran}`)
        .then(response => response.json())
        .then(data => {
            // Isi data ke dalam modal
            document.getElementById('editIdPelajaran').value = data.id_pelajaran;
            document.getElementById('editNamaPelajaran').value = data.nama_pelajaran;

            // Kosongkan dan isi ulang select guru
            const selectGuru = document.getElementById('editIdGuru');
            selectGuru.innerHTML = ''; // Kosongkan dulu

            data.guru.forEach(guru => {
                const option = document.createElement('option');
                option.value = guru.id_guru;
                option.textContent = guru.nama_guru;
                if (guru.id_guru === data.id_guru) {
                    option.selected = true; // Set sebagai terpilih
                }
                selectGuru.appendChild(option);
            });

            // Tampilkan modal
            const modal = new bootstrap.Modal(document.getElementById('modalEditPelajaran'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching pelajaran data:', error);
        });
}

    </script>
</body>
</html>
