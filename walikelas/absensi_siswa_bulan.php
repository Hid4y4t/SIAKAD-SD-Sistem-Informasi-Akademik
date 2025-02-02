<?php
require_once '../koneksi/koneksi.php';
$mysqli->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Periksa apakah pengguna login
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil parameter
$bulan = $_GET['bulan'] ?? null;
$id_kelas = $_GET['id_kelas'] ?? null;

if (!$bulan || !$id_kelas) {
    die("Parameter tidak valid.");
}

// Ambil nama kelas
$query_kelas = "SELECT nama_kelas FROM kelas WHERE id_kelas = ?";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_kelas);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();
$kelas = $result_kelas->fetch_assoc();
$nama_kelas = $kelas['nama_kelas'];

// Ambil data siswa berdasarkan kelas
// Ambil data siswa berdasarkan kelas
$query_siswa = "SELECT id_siswa, nama_siswa 
                FROM siswa 
                WHERE kelas = ? COLLATE utf8mb4_general_ci"; // Tambahkan COLLATE untuk memaksa kolasi yang sama

$stmt_siswa = $mysqli->prepare($query_siswa);
$stmt_siswa->bind_param("s", $nama_kelas);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();

$query_absensi = "
    SELECT id_siswa, tanggal, status 
    FROM absensi_siswa 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? 
    AND id_siswa IN (
        SELECT id_siswa 
        FROM siswa 
        WHERE kelas = ? COLLATE utf8mb4_general_ci
    )
    ORDER BY tanggal ASC";
$stmt_absensi = $mysqli->prepare($query_absensi);
$stmt_absensi->bind_param("ss", $bulan, $nama_kelas);
$stmt_absensi->execute();
$result_absensi = $stmt_absensi->get_result();

// Kelompokkan data absensi berdasarkan siswa dan tanggal
$absensi_data = [];
while ($row = $result_absensi->fetch_assoc()) {
    $absensi_data[$row['id_siswa']][$row['tanggal']] = $row['status'];
}

// Ambil tanggal unik untuk header tabel
$query_tanggal = "
    SELECT DISTINCT tanggal 
    FROM absensi_siswa 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? AND id_siswa IN (SELECT id_siswa FROM siswa WHERE kelas = ?)
    ORDER BY tanggal ASC";
$stmt_tanggal = $mysqli->prepare($query_tanggal);
$stmt_tanggal->bind_param("ss", $bulan, $nama_kelas);
$stmt_tanggal->execute();
$result_tanggal = $stmt_tanggal->get_result();

$tanggal_list = [];
while ($row = $result_tanggal->fetch_assoc()) {
    $tanggal_list[] = $row['tanggal'];
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
 <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->

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
                <h3>Absensi Bulan <?= date("F Y", strtotime($bulan)); ?> - <?= htmlspecialchars($nama_kelas); ?></h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                        <div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <?php foreach ($tanggal_list as $tanggal): ?>
                <th><?= date("d", strtotime($tanggal)); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($siswa = $result_siswa->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($siswa['nama_siswa']); ?></td>
                <?php foreach ($tanggal_list as $tanggal): ?>
                <td>
                    <?php
                    $status = $absensi_data[$siswa['id_siswa']][$tanggal] ?? null;
                    echo $status === 'Hadir' ? '✔' : ($status === 'Izin' ? 'I' : ($status === 'Alpha' ? 'A' : ($status === 'Sakit' ? 'S' : '-')));
                    ?>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="export_absensi.php?bulan=<?= $bulan; ?>&id_kelas=<?= $id_kelas; ?>" class="btn btn-success">Cetak ke Excel</a>
    <button class="btn btn-secondary" onclick="history.back()">Kembali</button>
</div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php include 'root/footer.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
