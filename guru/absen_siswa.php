<?php
require_once '../koneksi/koneksi.php';


session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil kode kelas dari URL
if (!isset($_GET['kode_kelas'])) {
    header("Location: data_kelas.php");
    exit;
}

$kode_kelas = $_GET['kode_kelas'];

// Ambil nama kelas berdasarkan kode kelas
$query_kelas = "SELECT nama_kelas FROM kelas WHERE kode_kelas = ?";
$stmt = $mysqli->prepare($query_kelas);
$stmt->bind_param("s", $kode_kelas);
$stmt->execute();
$result_kelas = $stmt->get_result();
$kelas = $result_kelas->fetch_assoc();

if (!$kelas) {
    echo "Kode kelas tidak valid.";
    exit;
}

$nama_kelas = $kelas['nama_kelas'];

// Jika data absensi disimpan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_absensi'])) {
    $tanggal = $_POST['tanggal'];

    // Cek apakah absensi sudah dilakukan untuk tanggal ini
    $query_cek_absensi = "SELECT COUNT(*) AS jumlah FROM absensi_siswa WHERE tanggal = ? AND id_siswa IN (SELECT id_siswa FROM siswa WHERE kelas = ?)";
    $stmt = $mysqli->prepare($query_cek_absensi);
    $stmt->bind_param("ss", $tanggal, $nama_kelas);
    $stmt->execute();
    $result_cek_absensi = $stmt->get_result();
    $data_cek_absensi = $result_cek_absensi->fetch_assoc();

    if ($data_cek_absensi['jumlah'] > 0) {
        echo "<script>alert('Absensi untuk hari ini sudah dilakukan!');</script>";
    } else {
        $absensi_data = $_POST['absensi'];

        foreach ($absensi_data as $id_siswa => $status) {
            $query_absensi = "INSERT INTO absensi_siswa (id_siswa, tanggal, status) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($query_absensi);
            $stmt->bind_param("iss", $id_siswa, $tanggal, $status);
            $stmt->execute();
        }

        echo "<script>alert('Absensi berhasil disimpan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
include 'root/head.php';
?>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include 'root/menu.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Absensi Siswa - <?= htmlspecialchars($nama_kelas); ?></h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Fitur Absensi</h4>
                                <div class="mb-3">
                                    <!-- Button Kembali -->
                                    <button class="btn btn-secondary" onclick="history.back()">Kembali</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Fitur Pertama: Modal untuk Input Absensi -->
                                <div id="absensi_action">
                                    <!-- Tombol atau Pesan akan dimanipulasi oleh JavaScript -->
                                    <button class="btn btn-primary mb-3" id="absensi_button" data-bs-toggle="modal"
                                        data-bs-target="#absensiModal">
                                        Input Absensi
                                    </button>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="absensiModal" tabindex="-1"
                                    aria-labelledby="absensiModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="absensiModalLabel">Input Absensi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="">
                                                <div class="modal-body">
                                                    <p><strong>Tanggal Absensi:</strong> <span
                                                            id="tanggal_hari_ini"></span></p>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Siswa</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                $query_siswa = "SELECT * FROM siswa WHERE kelas = ?";
                                                                $stmt = $mysqli->prepare($query_siswa);
                                                                $stmt->bind_param("s", $nama_kelas);
                                                                $stmt->execute();
                                                                $result_siswa = $stmt->get_result();
                                                                $no = 1;

                                                                while ($siswa = $result_siswa->fetch_assoc()): ?>
                                                            <tr>
                                                                <td><?= $no++; ?></td>
                                                                <td><?= htmlspecialchars($siswa['nama_siswa']); ?></td>
                                                                <td>
                                                                    <select name="absensi[<?= $siswa['id_siswa']; ?>]"
                                                                        class="form-select" required>
                                                                        <option value="Hadir">Hadir</option>
                                                                        <option value="Izin">Izin</option>
                                                                        <option value="Sakit">Sakit</option>
                                                                        <option value="Alpha">Alpha</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <input type="hidden" name="tanggal" id="tanggal_hidden">
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" name="save_absensi"
                                                        class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fitur Kedua: Tampilkan Absensi Berdasarkan Tanggal -->
                                <div class="mt-4">
                                    <h5>Lihat Absensi</h5>
                                    <form method="GET" action="">
                                        <input type="hidden" name="kode_kelas"
                                            value="<?= htmlspecialchars($kode_kelas); ?>">

                                        <!-- Pilihan Tahun, Bulan dan Tanggal -->
                                        <div class="mb-3">
                                            <label for="tanggal" class="form-label">Pilih Tanggal</label>
                                            <select name="tanggal" id="tanggal" class="form-select"
                                                onchange="this.form.submit()" required>
                                                <option value="" selected disabled>-- Pilih Tanggal --</option>
                                                <?php
                // Ambil semua tanggal yang tersedia di tabel absensi_siswa
                $query_tanggal = "SELECT DISTINCT DATE_FORMAT(tanggal, '%Y-%m-%d') AS tanggal FROM absensi_siswa WHERE id_siswa IN (SELECT id_siswa FROM siswa WHERE kelas = ?) ORDER BY tanggal DESC";
                $stmt = $mysqli->prepare($query_tanggal);
                $stmt->bind_param("s", $nama_kelas);
                $stmt->execute();
                $result_tanggal = $stmt->get_result();

                while ($row = $result_tanggal->fetch_assoc()):
                    $tanggal_value = $row['tanggal'];
                ?>
                                                <option value="<?= htmlspecialchars($tanggal_value); ?>"
                                                    <?= (isset($_GET['tanggal']) && $_GET['tanggal'] === $tanggal_value) ? 'selected' : ''; ?>>
                                                    <?= date("d F Y", strtotime($tanggal_value)); ?>
                                                </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </form>

                                    <?php if (isset($_GET['tanggal'])): ?>
                                    <?php
        $tanggal = $_GET['tanggal'];
        $query_absensi = "SELECT s.nama_siswa, a.status 
                          FROM absensi_siswa a 
                          JOIN siswa s ON a.id_siswa = s.id_siswa 
                          WHERE a.tanggal = ? AND s.kelas = ?";
        $stmt = $mysqli->prepare($query_absensi);
        $stmt->bind_param("ss", $tanggal, $nama_kelas);
        $stmt->execute();
        $result_absensi = $stmt->get_result();
        ?>
                                    <table class="table mt-3">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Siswa</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
            $no = 1;
            while ($absensi = $result_absensi->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($absensi['nama_siswa']); ?></td>
                                                <td><?= htmlspecialchars($absensi['status']); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php 
      include 'root/menu-mobile.php';
    include 'root/footer.php'; ?>
</body>
</html>

<script>
    // Set the default value for the date
    const dateToday = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_hidden').value = dateToday;
    document.getElementById('tanggal_hari_ini').innerText = dateToday;
</script>
