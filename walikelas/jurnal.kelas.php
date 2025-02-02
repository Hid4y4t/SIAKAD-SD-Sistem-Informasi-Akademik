<?php
require_once '../koneksi/koneksi.php';
session_start();
$mysqli->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

$id_guru = $_SESSION['id_guru'];

// Ambil data bulan dari tabel jurnal_kelas berdasarkan id_kelas yang sesuai
$query_bulan = "
    SELECT DISTINCT DATE_FORMAT(jk.tanggal, '%Y-%m') AS bulan, jk.id_kelas 
    FROM jurnal_kelas jk
    JOIN kelas k ON jk.id_kelas = k.id_kelas
    WHERE k.wali_kelas = ?
    ORDER BY bulan DESC
";
$stmt_bulan = $mysqli->prepare($query_bulan);
$stmt_bulan->bind_param("i", $id_guru);
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
                <h3>Jurnal Kelas</h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Daftar Bulan</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php while ($bulan = $result_bulan->fetch_assoc()): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
    <?= date("F Y", strtotime($bulan['bulan'])); ?>
    <div>
        <button class="btn btn-primary btn-sm" onclick="openTanggalModal('<?= $bulan['bulan']; ?>', <?= $bulan['id_kelas']; ?>)">
            Pilih Tanggal
        </button>
        <a href="export_jurnal_kelas.php?bulan=<?= $bulan['bulan']; ?>&id_kelas=<?= $bulan['id_kelas']; ?>" 
           class="btn btn-success btn-sm">
            Cetak ke Excel
        </a>
    </div>
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

    <!-- Modal Pilih Tanggal -->
    <div class="modal fade" id="tanggalModal" tabindex="-1" aria-labelledby="tanggalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tanggalModalLabel">Pilih Tanggal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group" id="tanggalList"></ul>
                </div>
            </div>
        </div>
    </div>

    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>-->
    <script>
        function openTanggalModal(bulan, idKelas) {
            fetch(`get_tanggal.php?bulan=${bulan}&id_kelas=${idKelas}`)
                .then(response => response.json())
                .then(data => {
                    const tanggalList = document.getElementById('tanggalList');
                    tanggalList.innerHTML = '';
                    data.forEach(tanggal => {
                        tanggalList.innerHTML += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${tanggal.tanggal}
                                <a href="lihat_jurnal.php?tanggal=${tanggal.tanggal}&id_kelas=${idKelas}" class="btn btn-info btn-sm">Lihat</a>
                            </li>
                        `;
                    });
                    const modal = new bootstrap.Modal(document.getElementById('tanggalModal'));
                    modal.show();
                });
        }
    </script>


<?php include 'root/footer.php' ?>
</body>

</html>
