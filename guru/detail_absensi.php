<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan mereka sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil parameter dari URL
$id_guru = $_GET['id_guru'] ?? null;
$bulan = $_GET['bulan'] ?? null;

if (!$id_guru || !$bulan) {
    die("Parameter bulan atau ID guru tidak ditemukan.");
}

// Query untuk mengambil data absensi
$query = "
    SELECT waktu_masuk, waktu_pulang, tanggal 
    FROM absensi_guru 
    WHERE id_guru = ? 
      AND DATE_FORMAT(tanggal, '%Y-%m') COLLATE utf8mb4_general_ci = ? 
    ORDER BY tanggal ASC
";


$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die("Query gagal dipersiapkan: " . $mysqli->error);
}

$stmt->bind_param("is", $id_guru, $bulan);
if (!$stmt->execute()) {
    die("Eksekusi query gagal: " . $stmt->error);
}

$result = $stmt->get_result();
if (!$result) {
    die("Gagal mengambil hasil: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
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
                <h3>Detail Absensi Bulan <?= date("F Y", strtotime($bulan)); ?></h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Data Absensi</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jam Masuk</th>
                                                <th>Jam Keluar</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result->num_rows > 0): ?>
                                                <?php $no = 1; ?>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td><?= $row['waktu_masuk']; ?></td>
                                                    <td><?= $row['waktu_pulang']; ?></td>
                                                    <td><?= $row['tanggal']; ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4">Tidak ada data absensi untuk bulan ini.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php include 'root/menu-mobile.php'; ?>
    <?php include 'root/footer.php'; ?>
    <?php include 'root/js.php'; ?>
</body>
</html>
s