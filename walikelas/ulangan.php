<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil ID wali kelas yang login
$id_guru = $_SESSION['id_guru'];

// Ambil data kelas yang diampu oleh wali kelas
$query_kelas = "SELECT id_kelas FROM kelas WHERE wali_kelas = ?";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_guru);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();

if ($result_kelas->num_rows === 0) {
    echo "Anda tidak memiliki kelas yang diampu.";
    exit;
}

$kelas = $result_kelas->fetch_assoc();
$id_kelas = $kelas['id_kelas'];

// Ambil data mata pelajaran berdasarkan kelas
$query_mapel = "SELECT nama_pelajaran FROM pelajaran WHERE id_kelas = ?";
$stmt_mapel = $mysqli->prepare($query_mapel);
$stmt_mapel->bind_param("i", $id_kelas);
$stmt_mapel->execute();
$result_mapel = $stmt_mapel->get_result();
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

          

            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <!-- Tabel Data Mata Pelajaran -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Mata Pelajaran Kelas <?= htmlspecialchars($id_kelas); ?></h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" id="table1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pelajaran</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php while ($mapel = $result_mapel->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($mapel['nama_pelajaran']); ?></td>
                                            <td>
                                                <a href="lihat_nilai.php?id_kelas=<?= $id_kelas; ?>&nama_pelajaran=<?= urlencode($mapel['nama_pelajaran']); ?>"
                                                    class="btn btn-info btn-sm">Lihat Nilai</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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
