<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil daftar kelas
$query_kelas = "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
$result_kelas = $mysqli->query($query_kelas);

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
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
                <h3>Daftar Kelas</h3>
            </div>

            <div class="page-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Kelas</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php while ($kelas = $result_kelas->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($kelas['nama_kelas']); ?>
                                        <a href="detail_mapel.php?id_kelas=<?= $kelas['id_kelas']; ?>" class="btn btn-primary btn-sm">
                                            Lihat Detail
                                        </a>
                                    </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'root/footer.php'; ?>
        </div>
    </div>
</body>
</html>
