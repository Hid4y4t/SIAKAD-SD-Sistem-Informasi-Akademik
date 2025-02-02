<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil parameter id_siswa
if (!isset($_GET['id_siswa'])) {
    echo "<script>alert('Siswa tidak valid.'); window.history.back();</script>";
    exit;
}

$id_siswa = $_GET['id_siswa'];

// Ambil data siswa
$query_siswa = "SELECT nama_siswa, kelas FROM siswa WHERE id_siswa = ?";
$stmt_siswa = $mysqli->prepare($query_siswa);
$stmt_siswa->bind_param("i", $id_siswa);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();
$siswa = $result_siswa->fetch_assoc();

if (!$siswa) {
    echo "<script>alert('Siswa tidak ditemukan.'); window.history.back();</script>";
    exit;
}

// Ambil data catatan wali kelas untuk siswa tertentu
$query_catatan = "SELECT * FROM catatan_wali_kelas WHERE id_siswa = ? ORDER BY tanggal DESC";
$stmt_catatan = $mysqli->prepare($query_catatan);
$stmt_catatan->bind_param("i", $id_siswa);
$stmt_catatan->execute();
$result_catatan = $stmt_catatan->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'root/head.php'; ?>
    <title>Detail Catatan Wali Kelas</title>
</head>

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
                <h3>Catatan Wali Kelas</h3>
                <p>Siswa: <strong><?= htmlspecialchars($siswa['nama_siswa']); ?></strong> | Kelas: <strong><?= htmlspecialchars($siswa['kelas']); ?></strong></p>
            </div>

            <div class="page-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Detail Catatan</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Permasalahan</th>
                                            <th>Solusi</th>
                                            <th>Catatan Tambahan</th>
                                            <th>Dilaporkan Kepada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result_catatan->num_rows > 0): ?>
                                            <?php $no = 1; while ($catatan = $result_catatan->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td><?= htmlspecialchars($catatan['tanggal']); ?></td>
                                                    <td><?= htmlspecialchars($catatan['permasalahan']); ?></td>
                                                    <td><?= htmlspecialchars($catatan['solusi']); ?></td>
                                                    <td><?= htmlspecialchars($catatan['catatan_tambahan'] ?: '-'); ?></td>
                                                    <td><?= htmlspecialchars($catatan['dilaporkan_kepada']); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada catatan untuk siswa ini.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <button class="btn btn-secondary mt-3" onclick="history.back()">Kembali</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'root/menu-mobile.php'; ?>
    <?php include 'root/footer.php'; ?>
</body>

</html>
