<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}
if (!isset($_GET['kode_kelas'])) {
    echo "<script>alert('Kode kelas tidak ditemukan.'); window.location.href='data_kelas.php';</script>";
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
    echo "<script>alert('Kode kelas tidak valid.'); window.location.href='data_kelas.php';</script>";
    exit;
}

$id_kelas = $kelas['id_kelas'];
$nama_kelas = $kelas['nama_kelas'];

// Ambil ID guru dari session
$id_guru = $_SESSION['id_guru'];

// Ambil data mata pelajaran yang diajar oleh guru ini
$query_mapel = "SELECT DISTINCT nama_pelajaran AS mapel FROM pelajaran WHERE id_kelas = ?";
$stmt = $mysqli->prepare($query_mapel);
$stmt->bind_param("i", $id_kelas);
$stmt->execute();
$result_mapel = $stmt->get_result();


// Ambil data kelas yang diajar oleh guru ini
$query_kelas = "SELECT DISTINCT k.nama_kelas, k.id_kelas 
                FROM kelas k 
                JOIN pelajaran p ON k.id_kelas = p.id_kelas 
                WHERE p.id_guru = ?";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_guru);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_nilai'])) {
    $mapel = $_POST['mapel'];
    $tanggal = date('Y-m-d'); // Tanggal hari ini
    $nilai_data = $_POST['nilai'] ?? [];
    $catatan_data = $_POST['catatan'] ?? [];

    if ($mapel && $nilai_data) {
        foreach ($nilai_data as $id_siswa => $nilai) {
            $catatan = $catatan_data[$id_siswa] ?? '';
            $query_insert = "INSERT INTO nilai_ulangan_harian (id_siswa, id_guru, mapel, id_kelas, tanggal, nilai, catatan) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $mysqli->prepare($query_insert);
            $stmt_insert->bind_param("iisisds", $id_siswa, $id_guru, $mapel, $id_kelas, $tanggal, $nilai, $catatan);

            if (!$stmt_insert->execute()) {
                echo "<script>alert('Gagal menyimpan nilai: {$stmt_insert->error}');</script>";
            }
        }
        echo "<script>alert('Nilai berhasil disimpan!');</script>";
    } else {
        echo "<script>alert('Data tidak lengkap. Pastikan mata pelajaran dan nilai diisi.');</script>";
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
                <h3>Nilai Harian</h3>
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#historyModal">Lihat
                                    History Nilai</button>
            </div>

            <div class="page-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Input Nilai Harian</h4>
                            </div>
                            <div class="card-body">
                            <form method="POST" action="">
    <div class="mb-3">
        <label for="mapel" class="form-label">Pilih Mata Pelajaran</label>
        <select name="mapel" id="mapel" class="form-select" required>
            <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
            <?php while ($mapel = $result_mapel->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($mapel['mapel']); ?>"><?= htmlspecialchars($mapel['mapel']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div id="table-container" class="d-none">
        <h5>Input Nilai dan Catatan</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody id="table-body"></tbody>
        </table>
    </div>
    <button type="submit" name="save_nilai" class="btn btn-primary d-none" id="save-button">Simpan</button>
</form>


                            </div>
                        </div>

                      
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal History -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Pilih Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <?php
                    $result_mapel->data_seek(0); // Reset pointer untuk hasil query mapel
                    while ($mapel = $result_mapel->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($mapel['mapel']); ?>
                            <a href="daftar_nilai.php?id_kelas=<?= $id_kelas; ?>&id_guru=<?= $id_guru; ?>&mapel=<?= urlencode($mapel['mapel']); ?>"
                                class="btn btn-info btn-sm">
                                Lihat
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal untuk Menampilkan Nilai -->
    <div class="modal fade" id="nilaiModal" tabindex="-1" aria-labelledby="nilaiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nilaiModalLabel">Daftar Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Nilai</th>
                                <th>Catatan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="nilaiTableBody">
                            <tr>
                                <td colspan="4">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
            include 'root/menu-mobile.php';
include 'root/footer.php'
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', () => {
    const mapelSelect = document.getElementById('mapel');
    const tableContainer = document.getElementById('table-container');
    const tableBody = document.getElementById('table-body');
    const saveButton = document.getElementById('save-button');

    mapelSelect.addEventListener('change', async () => {
        tableBody.innerHTML = ''; // Bersihkan tabel sebelumnya
        tableContainer.classList.add('d-none');
        saveButton.classList.add('d-none');

        const response = await fetch(`get_siswa.php?id_kelas=<?= $id_kelas; ?>`);
        const siswa = await response.json();

        if (siswa.length > 0) {
            siswa.forEach((s) => {
                const row = `
                    <tr>
                        <td>${s.nama_siswa}</td>
                        <td><input type="number" name="nilai[${s.id_siswa}]" class="form-control" required></td>
                        <td><input type="text" name="catatan[${s.id_siswa}]" class="form-control"></td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
            tableContainer.classList.remove('d-none');
            saveButton.classList.remove('d-none');
        } else {
            tableBody.innerHTML = '<tr><td colspan="3">Tidak ada siswa untuk kelas ini.</td></tr>';
        }
    });
});



    document.addEventListener('DOMContentLoaded', () => {
        const btnLihatNilai = document.querySelectorAll('.btn-lihat-nilai');
        const nilaiTableBody = document.getElementById('nilaiTableBody');

        btnLihatNilai.forEach(button => {
            button.addEventListener('click', async () => {
                const mapel = button.getAttribute('data-mapel');
                const idKelas = document.getElementById('id_kelas').value;
                const idGuru = <?= $id_guru; ?>;

                // Kosongkan tabel nilai
                nilaiTableBody.innerHTML = '<tr><td colspan="4">Loading...</td></tr>';

                // Fetch data nilai dari server
                const response = await fetch(
                    `get_nilai.php?id_kelas=${idKelas}&id_guru=${idGuru}&mapel=${encodeURIComponent(mapel)}`
                    );
                const data = await response.json();

                // Update tabel dengan data nilai
                if (data.length > 0) {
                    nilaiTableBody.innerHTML = '';
                    data.forEach(item => {
                        const row = `
                        <tr>
                            <td>${item.nama_siswa}</td>
                            <td>${item.nilai}</td>
                            <td>${item.catatan}</td>
                            <td>${item.tanggal}</td>
                        </tr>
                    `;
                        nilaiTableBody.innerHTML += row;
                    });
                } else {
                    nilaiTableBody.innerHTML =
                        '<tr><td colspan="4">Tidak ada data nilai untuk mapel ini.</td></tr>';
                }

                // Tampilkan modal nilai
                const nilaiModal = new bootstrap.Modal(document.getElementById(
                    'nilaiModal'));
                nilaiModal.show();
            });
        });
    });


    </script>

<?php
include 'root/js.php'
?>
</body>

</html>