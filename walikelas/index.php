<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil ID guru yang login
$id_guru = $_SESSION['id_guru'];

// Ambil data kelas yang diampu oleh wali kelas yang login
$query_kelas = "
    SELECT id_kelas, nama_kelas 
    FROM kelas 
    WHERE wali_kelas = ?
";
$stmt_kelas = $mysqli->prepare($query_kelas);
$stmt_kelas->bind_param("i", $id_guru);
$stmt_kelas->execute();
$result_kelas = $stmt_kelas->get_result();
$kelas = $result_kelas->fetch_assoc();

if (!$kelas) {
    echo "Anda tidak memiliki kelas yang diampu.";
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "root/head.php"; ?>
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include "root/menu.php"; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Dashboard Wali Kelas</h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <!-- Ringkasan Kelas yang Diampu -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Ringkasan Kelas yang Diampu</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                require_once '../koneksi/koneksi.php';

                                // Ambil data kelas yang diampu oleh wali kelas
                               
                                $id_guru = $_SESSION['id_guru'];
                                $query_kelas = "SELECT nama_kelas, tingkat, tahun_ajaran FROM kelas WHERE wali_kelas = ?";
                                $stmt = $mysqli->prepare($query_kelas);
                                $stmt->bind_param("i", $id_guru);
                                $stmt->execute();
                                $result_kelas = $stmt->get_result();

                                if ($result_kelas->num_rows > 0):
                                ?>
                                <table class="table table-striped" id="table3">
                                    <thead>
                                        <tr>
                                            <th>Kelas</th>
                                            <th>Tingkat</th>
                                            <th>Tahun Ajaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($kelas = $result_kelas->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($kelas['nama_kelas']); ?></td>
                                            <td><?= htmlspecialchars($kelas['tingkat']); ?></td>
                                            <td><?= htmlspecialchars($kelas['tahun_ajaran']); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <p>Anda tidak memiliki kelas yang diampu.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Absensi Terkini -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Absensi Terkini</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                // Ambil data absensi terkini untuk kelas yang diampu
                                $query_absensi = "
                                    SELECT s.nama_siswa, a.tanggal, a.status 
                                    FROM absensi_siswa a
                                    JOIN siswa s ON a.id_siswa = s.id_siswa
                                    WHERE s.kelas IN (SELECT nama_kelas FROM kelas WHERE wali_kelas = ?)
                                    ORDER BY a.tanggal 
                                ";
                                $stmt_absensi = $mysqli->prepare($query_absensi);
                                $stmt_absensi->bind_param("i", $id_guru);
                                $stmt_absensi->execute();
                                $result_absensi = $stmt_absensi->get_result();

                                if ($result_absensi->num_rows > 0):
                                ?>
                                <table class="table table-striped" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Nama Siswa</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($absensi = $result_absensi->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($absensi['nama_siswa']); ?></td>
                                            <td><?= htmlspecialchars($absensi['tanggal']); ?></td>
                                            <td><?= htmlspecialchars($absensi['status']); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <p>Tidak ada data absensi terkini.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php include "root/footer.php"; ?>
</body>

</html>
