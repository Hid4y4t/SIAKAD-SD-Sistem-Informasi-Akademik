<?php
require_once '../koneksi/koneksi.php';

$tanggal = $_GET['tanggal'] ?? null;
$id_kelas = $_GET['id_kelas'] ?? null;

if (!$tanggal || !$id_kelas) {
    die("Parameter tidak valid.");
}

// Query untuk mengambil data jurnal kelas berdasarkan tanggal dan id_kelas
$query = "
    SELECT 
        nama_mapel, 
        jam_pelajaran, 
        topik_pembahasan, 
        catatan 
    FROM 
        jurnal_kelas 
    WHERE 
        tanggal = ? AND id_kelas = ?
    ORDER BY jam_pelajaran ASC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $tanggal, $id_kelas);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->

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
                <h3>Jurnal Kelas - <?= htmlspecialchars($tanggal); ?></h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Detail Jurnal</h4>
                                <a href="export_jurnal.php?tanggal=<?= $tanggal; ?>&id_kelas=<?= $id_kelas; ?>" class="btn btn-success">Cetak ke Excel</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Mata Pelajaran</th>
                                            <th>Jam Pelajaran</th>
                                            <th>Topik Pembahasan</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($jurnal = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($jurnal['nama_mapel']); ?></td>
                                            <td><?= htmlspecialchars($jurnal['jam_pelajaran']); ?></td>
                                            <td><?= htmlspecialchars($jurnal['topik_pembahasan']); ?></td>
                                            <td><?= htmlspecialchars($jurnal['catatan']); ?></td>
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
</body>

</html>
