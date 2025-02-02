<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan apakah jabatan mereka sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil kode kelas dari parameter URL
if (!isset($_GET['kode_kelas'])) {
    header("Location: jurnal_kelas.php"); // Arahkan kembali jika kode kelas tidak ada
    exit;
}

$kode_kelas = $_GET['kode_kelas'];

// Ambil data kelas berdasarkan kode_kelas
$query = "SELECT * FROM kelas WHERE kode_kelas = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $kode_kelas);
$stmt->execute();
$result = $stmt->get_result();
$kelas = $result->fetch_assoc();

if (!$kelas) {
    echo "Kode kelas tidak valid.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<?php
include 'root/head.php';
?>

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
                <h3>Data Kelas - <?= htmlspecialchars($kelas['nama_kelas']); ?></h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Fitur untuk Kelas</h4>
                            </div>
                            <div class="card-body">
                                <p>Kode Kelas: <strong><?= htmlspecialchars($kelas['kode_kelas']); ?></strong></p>
                                <p>Tingkat: <strong><?= htmlspecialchars($kelas['tingkat']); ?></strong></p>
                                <p>Tahun Ajaran: <strong><?= htmlspecialchars($kelas['tahun_ajaran']); ?></strong></p>

                                <div class="row text-center mt-4">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="absen_siswa.php?kode_kelas=<?= urlencode($kelas['kode_kelas']); ?>" class="btn btn-primary btn-lg w-100 py-4">
                                            <i class="bi bi-check-circle-fill"></i> 
                                            <br>Absen Siswa
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="jurnal_kelas.php?kode_kelas=<?= urlencode($kelas['kode_kelas']); ?>" class="btn btn-success btn-lg w-100 py-4">
                                            <i class="bi bi-journal"></i> 
                                            <br>Jurnal Kelas
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="catatan_siswa.php?kode_kelas=<?= urlencode($kelas['kode_kelas']); ?>" class="btn btn-warning btn-lg w-100 py-4">
                                            <i class="bi bi-bookmark"></i> 
                                            <br>Catatan Siswa
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="nilai_harian.php?kode_kelas=<?= urlencode($kelas['kode_kelas']); ?>" class="btn btn-info btn-lg w-100 py-4">
                                            <i class="bi bi-bar-chart-line"></i> 
                                            <br>Nilai Harian
                                        </a>
                                    </div>
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
include 'root/footer.php'
?>
</body>

</html>
