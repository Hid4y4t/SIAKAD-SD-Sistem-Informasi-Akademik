<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Wali Kelas') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil parameter dari URL
$id_kelas = $_GET['id_kelas'] ?? null;
$nama_pelajaran = $_GET['nama_pelajaran'] ?? null;

// Validasi parameter
if (!$id_kelas || !$nama_pelajaran) {
    echo "Parameter tidak valid.";
    exit;
}

// Ambil data siswa berdasarkan kelas
$query_siswa = "SELECT id_siswa, nama_siswa FROM siswa WHERE kelas = (SELECT nama_kelas FROM kelas WHERE id_kelas = ?)";
$stmt_siswa = $mysqli->prepare($query_siswa);
$stmt_siswa->bind_param("i", $id_kelas);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();

// Ambil data nilai berdasarkan mata pelajaran dan kelas
$query_nilai = "SELECT id_siswa, tanggal, nilai FROM nilai_ulangan_harian WHERE id_kelas = ? AND mapel = ? ORDER BY tanggal ASC";
$stmt_nilai = $mysqli->prepare($query_nilai);
$stmt_nilai->bind_param("is", $id_kelas, $nama_pelajaran);
$stmt_nilai->execute();
$result_nilai = $stmt_nilai->get_result();

$nilai_data = [];
while ($row = $result_nilai->fetch_assoc()) {
    $nilai_data[$row['id_siswa']][$row['tanggal']] = $row['nilai'];
}

// Ambil tanggal-tanggal unik
$tanggal_query = "SELECT DISTINCT tanggal FROM nilai_ulangan_harian WHERE id_kelas = ? AND mapel = ? ORDER BY tanggal ASC";
$stmt_tanggal = $mysqli->prepare($tanggal_query);
$stmt_tanggal->bind_param("is", $id_kelas, $nama_pelajaran);
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
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Nilai</h3>

                    </div>
                    <div class="card-body">
                        <p>Nama Mapel: <strong><?= htmlspecialchars($nama_pelajaran); ?></strong> | Kelas:
                            <strong><?= htmlspecialchars($id_kelas); ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="page-content">
                <div class="card">
                    <div class="card-header">
                        <h4>Nilai Siswa</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <a href="export_nilai_excel.php?id_kelas=<?= $id_kelas; ?>&nama_pelajaran=<?= urlencode($nama_pelajaran); ?>"
                                class="btn btn-success">
                                Cetak ke Excel
                            </a>
                        </div>
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <?php foreach ($tanggal_list as $tanggal): ?>
                                    <th><?= date("d-m-Y", strtotime($tanggal)); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($siswa = $result_siswa->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($siswa['nama_siswa']); ?></td>
                                    <?php foreach ($tanggal_list as $tanggal): ?>
                                    <td>
                                        <?= $nilai_data[$siswa['id_siswa']][$tanggal] ?? '-'; ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <button class="btn btn-secondary" onclick="history.back()">Kembali</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'root/footer.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>