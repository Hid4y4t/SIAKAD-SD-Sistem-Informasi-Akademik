<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil kode_kelas dari URL
if (!isset($_GET['kode_kelas'])) {
    header("Location: data_kelas.php");
    exit;
}

$kode_kelas = $_GET['kode_kelas'];

// Ambil id_kelas berdasarkan kode_kelas
$query_kelas = "SELECT id_kelas, nama_kelas FROM kelas WHERE kode_kelas = ?";
$stmt = $mysqli->prepare($query_kelas);
$stmt->bind_param("s", $kode_kelas);
$stmt->execute();
$result_kelas = $stmt->get_result();
$kelas = $result_kelas->fetch_assoc();

if (!$kelas) {
    echo "Kode kelas tidak valid.";
    exit;
}

$id_kelas = $kelas['id_kelas'];
$nama_kelas = $kelas['nama_kelas'];

// Ambil data siswa berdasarkan id_kelas
$query_siswa = "SELECT * FROM siswa WHERE kelas = ?";
$stmt = $mysqli->prepare($query_siswa);
$stmt->bind_param("s", $nama_kelas);
$stmt->execute();
$result_siswa = $stmt->get_result();



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_catatan'])) {
    // Ambil data dari form
    $id_siswa = $_POST['id_siswa'];
    $id_guru = $_POST['id_guru'];
    $id_kelas = $_POST['id_kelas'];
    $catatan_guru = $_POST['catatan_guru'];
    $tanggal = date('Y-m-d'); // Gunakan tanggal hari ini

    // Query untuk menyimpan data
    $query = "INSERT INTO catatan_siswa (id_siswa, id_guru, id_kelas, tanggal, catatan_guru) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);

    if (!$stmt) {
        die("Kesalahan persiapan query: " . $mysqli->error);
    }

    $stmt->bind_param("iiiss", $id_siswa, $id_guru, $id_kelas, $tanggal, $catatan_guru);

    if ($stmt->execute()) {
        echo "<script>alert('Catatan berhasil disimpan!');</script>";
    } else {
        echo "<script>alert('Gagal menyimpan catatan: " . $stmt->error . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

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
                <h3>Catatan Siswa Kelas - <?= htmlspecialchars($nama_kelas); ?></h3>
            </div>

            <div class="page-content">

                <div class="">
                    <div class="mb-3">
                        <!-- Button Kembali -->
                        <button class="btn btn-secondary" onclick="history.back()">Kembali</button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                            $no = 1;
                                            while ($siswa = $result_siswa->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($siswa['nama_siswa']); ?></td>
                                        <td>
                                            <!-- Button Tambah Catatan -->
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#tambahCatatanModal-<?= $siswa['id_siswa']; ?>">Tambah
                                            </button>
                                            <!-- Button Lihat Catatan -->
                                            <button class="btn btn-info btn-sm btn-lihat-catatan"
                                                data-id-siswa="<?= $siswa['id_siswa']; ?>"
                                                data-nama-siswa="<?= htmlspecialchars($siswa['nama_siswa']); ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#lihatCatatanModal">Lihat</button>
                                        </td>
                                    </tr>

                                    <!-- Modal Tambah Catatan -->
                                    <div class="modal fade" id="tambahCatatanModal-<?= $siswa['id_siswa']; ?>"
                                        tabindex="-1"
                                        aria-labelledby="tambahCatatanModalLabel-<?= $siswa['id_siswa']; ?>"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="tambahCatatanModalLabel-<?= $siswa['id_siswa']; ?>">
                                                        Tambah</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_siswa"
                                                            value="<?= $siswa['id_siswa']; ?>">
                                                        <input type="hidden" name="id_kelas" value="<?= $id_kelas; ?>">
                                                        <input type="hidden" name="id_guru"
                                                            value="<?= $_SESSION['id_guru']; ?>">
                                                        <div class="mb-3">
                                                            <label for="catatan_guru-<?= $siswa['id_siswa']; ?>"
                                                                class="form-label">Catatan Guru</label>
                                                            <textarea name="catatan_guru"
                                                                id="catatan_guru-<?= $siswa['id_siswa']; ?>"
                                                                class="form-control" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                        <button type="submit" name="save_catatan"
                                                            class="btn btn-primary">Simpan</button>
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
            </div>

        </div>
    </div>

    <!-- Modal Lihat Catatan -->
    <div class="modal fade" id="lihatCatatanModal" tabindex="-1" aria-labelledby="lihatCatatanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lihatCatatanModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 id="namaSiswa"></h5>

                    <table class="table table-striped" id="catatanTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php
            include 'root/menu-mobile.php';
include 'root/footer.php'
?>
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const lihatButtons = document.querySelectorAll('.btn-lihat-catatan');
        lihatButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const idSiswa = button.getAttribute('data-id-siswa');
                const namaSiswa = button.getAttribute('data-nama-siswa');
                document.getElementById('namaSiswa').innerText =
                    `Catatan untuk ${namaSiswa}`;

                const tableBody = document.querySelector('#catatanTable tbody');
                tableBody.innerHTML = '<tr><td colspan="2">Loading...</td></tr>';

                const response = await fetch(
                    `get_catatan.php?id_siswa=${idSiswa}&id_guru=<?= $_SESSION['id_guru']; ?>&id_kelas=<?= $id_kelas; ?>`
                );
                const data = await response.json();

                if (data.length > 0) {
                    tableBody.innerHTML = '';
                    data.forEach(catatan => {
                        const row = `<tr>
                                <td>${catatan.tanggal}</td>
                                <td>${catatan.catatan_guru}</td>
                            </tr>`;
                        tableBody.innerHTML += row;
                    });
                } else {
                    tableBody.innerHTML =
                        '<tr><td colspan="2">Belum ada catatan.</td></tr>';
                }
            });
        });
    });
    </script>
</body>

</html>