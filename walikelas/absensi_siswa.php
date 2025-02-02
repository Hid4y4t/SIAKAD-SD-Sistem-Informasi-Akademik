<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna login
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil ID wali kelas
$id_guru = $_SESSION['id_guru'];

// Ambil kelas wali kelas
$query_kelas = "SELECT id_kelas, nama_kelas FROM kelas WHERE wali_kelas = ?";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_guru);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();
$kelas = $result_kelas->fetch_assoc();

if (!$kelas) {
    echo "Anda tidak memiliki kelas yang diampu.";
    exit;
}

$id_kelas = $kelas['id_kelas'];
$nama_kelas = $kelas['nama_kelas'];

// Ambil daftar bulan dari absensi siswa berdasarkan kelas
$query_bulan = "
    SELECT DISTINCT DATE_FORMAT(tanggal, '%Y-%m') AS bulan 
    FROM absensi_siswa 
    WHERE id_siswa IN (SELECT id_siswa FROM siswa WHERE kelas = ?)
    ORDER BY bulan ASC";
$stmt_bulan = $mysqli->prepare($query_bulan);
$stmt_bulan->bind_param("s", $nama_kelas);
$stmt_bulan->execute();
$result_bulan = $stmt_bulan->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->

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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Pilih Bulan</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php while ($bulan = $result_bulan->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= date("F Y", strtotime($bulan['bulan'])); ?>
                                        <a href="absensi_siswa_bulan.php?bulan=<?= $bulan['bulan']; ?>&id_kelas=<?= $id_kelas; ?>" class="btn btn-primary btn-sm">Lihat Absensi</a>
                                    </li>
                                    <?php endwhile; ?>
                                </ul>
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
