<?php
require_once '../koneksi/koneksi.php';
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit;
}

// Ambil data kelas beserta nama wali kelas dari tabel guru
$query_kelas = "
    SELECT 
        k.id_kelas, k.nama_kelas, k.kode_kelas, k.tingkat, 
        k.tahun_ajaran, k.wali_kelas, g.nama_guru 
    FROM kelas k
    LEFT JOIN guru g ON k.wali_kelas = g.id_guru
";
$result_kelas = $mysqli->query($query_kelas);

// Proses hapus kelas
if (isset($_POST['hapus_kelas'])) {
    $id_kelas = $_POST['id_kelas'];
    $query_hapus = "DELETE FROM kelas WHERE id_kelas = ?";
    $stmt_hapus = $mysqli->prepare($query_hapus);
    $stmt_hapus->bind_param("i", $id_kelas);
    $stmt_hapus->execute();
    header("Location: kelas.php");
    exit;
}

// Proses tambah kelas
if (isset($_POST['tambah_kelas'])) {
    $nama_kelas = $_POST['nama_kelas'];
    $kode_kelas = $_POST['kode_kelas'];
    $tingkat = $_POST['tingkat'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $wali_kelas = $_POST['wali_kelas'];

    $query_tambah = "INSERT INTO kelas (nama_kelas, kode_kelas, tingkat, tahun_ajaran, wali_kelas) VALUES (?, ?, ?, ?, ?)";
    $stmt_tambah = $mysqli->prepare($query_tambah);
    $stmt_tambah->bind_param("ssssi", $nama_kelas, $kode_kelas, $tingkat, $tahun_ajaran, $wali_kelas);
    $stmt_tambah->execute();
    header("Location: kelas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->

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
                <h3>Manajemen Kelas</h3>
            </div>
            <div class="page-content">
                <div class="row">
                    <div class="col-12">
                        <!-- Tombol Tambah Kelas -->
                        <div class="mb-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                                Tambah Kelas
                            </button>
                        </div>

                        <!-- Tabel Data Kelas -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Kelas</h4>
                            </div>
                            <div class="card-body">
                                <table class="table" id="table2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kelas</th>
                                            <th>Kode Kelas</th>
                                            <th>Tingkat</th>
                                            <th>Tahun Ajaran</th>
                                            <th>Wali Kelas</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php while ($kelas = $result_kelas->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($kelas['nama_kelas']); ?></td>
                                                <td><?= htmlspecialchars($kelas['kode_kelas']); ?></td>
                                                <td><?= htmlspecialchars($kelas['tingkat']); ?></td>
                                                <td><?= htmlspecialchars($kelas['tahun_ajaran']); ?></td>
                                                <td><?= htmlspecialchars($kelas['nama_guru'] ?? 'Belum Ditentukan'); ?></td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm"
                                                        onclick="openEditModal(<?= $kelas['id_kelas']; ?>, '<?= htmlspecialchars($kelas['nama_kelas']); ?>', '<?= htmlspecialchars($kelas['kode_kelas']); ?>', '<?= htmlspecialchars($kelas['tingkat']); ?>', '<?= htmlspecialchars($kelas['tahun_ajaran']); ?>', <?= $kelas['wali_kelas'] ?? 0; ?>)">
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="" class="d-inline">
                                                        <input type="hidden" name="id_kelas" value="<?= $kelas['id_kelas']; ?>">
                                                        <button type="submit" name="hapus_kelas" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                                            Hapus
                                                        </button>
                                                    </form>
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
        </div>
    </div>

    <!-- Modal Tambah Kelas -->
    <div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-labelledby="modalTambahKelasLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahKelasLabel">Tambah Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" required>
                        </div>
                        <div class="mb-3">
                            <label for="kode_kelas" class="form-label">Kode Kelas</label>
                            <input type="text" class="form-control" id="kode_kelas" name="kode_kelas" required>
                        </div>
                        <div class="mb-3">
                            <label for="tingkat" class="form-label">Tingkat</label>
                            <input type="text" class="form-control" id="tingkat" name="tingkat" required>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" required>
                        </div>
                        <div class="mb-3">
                            <label for="wali_kelas" class="form-label">Wali Kelas</label>
                            <select name="wali_kelas" id="wali_kelas" class="form-select" required>
                                <option value="">Pilih Wali Kelas</option>
                                <?php
                                $query_guru = "SELECT id_guru, nama_guru FROM guru";
                                $result_guru = $mysqli->query($query_guru);
                                while ($guru = $result_guru->fetch_assoc()):
                                ?>
                                    <option value="<?= $guru['id_guru']; ?>"><?= htmlspecialchars($guru['nama_guru']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_kelas" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kelas -->
<div class="modal fade" id="modalEditKelas" tabindex="-1" aria-labelledby="modalEditKelasLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="proses_edit_kelas.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditKelasLabel">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="editModalBody">
                    <input type="hidden" name="id_kelas" id="edit_id_kelas">
                    <div class="mb-3">
                        <label for="edit_nama_kelas" class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" id="edit_nama_kelas" name="nama_kelas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_kode_kelas" class="form-label">Kode Kelas</label>
                        <input type="text" class="form-control" id="edit_kode_kelas" name="kode_kelas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tingkat" class="form-label">Tingkat</label>
                        <input type="text" class="form-control" id="edit_tingkat" name="tingkat" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tahun_ajaran" class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control" id="edit_tahun_ajaran" name="tahun_ajaran" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_wali_kelas" class="form-label">Wali Kelas</label>
                        <select name="wali_kelas" id="edit_wali_kelas" class="form-select" required>
                            <option value="">Pilih Wali Kelas</option>
                            <?php
                            $query_guru = "SELECT id_guru, nama_guru FROM guru";
                            $result_guru = $mysqli->query($query_guru);
                            while ($guru = $result_guru->fetch_assoc()):
                            ?>
                                <option value="<?= $guru['id_guru']; ?>"><?= htmlspecialchars($guru['nama_guru']); ?></option>
                            <?php endwhile; ?>
                        </select>
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

    <?php
include 'root/footer.php'
?>




        </div>
    </div>

    <?php
include 'root/js.php'
?>
    <script>
       function openEditModal(idKelas, namaKelas, kodeKelas, tingkat, tahunAjaran, waliKelas) {
    document.getElementById('edit_id_kelas').value = idKelas;
    document.getElementById('edit_nama_kelas').value = namaKelas;
    document.getElementById('edit_kode_kelas').value = kodeKelas;
    document.getElementById('edit_tingkat').value = tingkat;
    document.getElementById('edit_tahun_ajaran').value = tahunAjaran;

    // Set selected wali kelas
    const waliKelasSelect = document.getElementById('edit_wali_kelas');
    for (const option of waliKelasSelect.options) {
        option.selected = parseInt(option.value) === parseInt(waliKelas);
    }

    const modal = new bootstrap.Modal(document.getElementById('modalEditKelas'));
    modal.show();
}

    </script>
</body>
</html>
