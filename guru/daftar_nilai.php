<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil parameter dari URL
$id_kelas = $_GET['id_kelas'];
$id_guru = $_GET['id_guru'];
$mapel = $_GET['mapel'];

// Validasi parameter
if (empty($id_kelas) || empty($id_guru) || empty($mapel)) {
    echo "Parameter tidak valid.";
    exit;
}

// Proses hapus data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    $delete_ids = $_POST['delete_ids'] ?? [];
    if (!empty($delete_ids)) {
        $placeholders = implode(',', array_fill(0, count($delete_ids), '?'));
        $query_delete = "DELETE FROM nilai_ulangan_harian WHERE id_ulangan IN ($placeholders)";
        $stmt_delete = $mysqli->prepare($query_delete);
        $stmt_delete->bind_param(str_repeat('i', count($delete_ids)), ...$delete_ids);
        if ($stmt_delete->execute()) {
            echo "<script>alert('Data terpilih berhasil dihapus.'); window.location.reload();</script>";
        } else {
            echo "<script>alert('Gagal menghapus data: {$stmt_delete->error}');</script>";
        }
    } else {
        echo "<script>alert('Tidak ada data yang dipilih untuk dihapus.');</script>";
    }
}

// Ambil data nilai berdasarkan parameter
$query = "
    SELECT 
        n.id_ulangan, 
        s.nama_siswa, 
        n.nilai, 
        n.catatan, 
        n.tanggal 
    FROM 
        nilai_ulangan_harian n
    JOIN 
        siswa s ON n.id_siswa = s.id_siswa
    WHERE 
        n.id_kelas = ? AND n.id_guru = ? AND n.mapel = ?
    ORDER BY n.tanggal DESC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iis", $id_kelas, $id_guru, $mapel);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

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
                <h3>Daftar Nilai</h3>
                <button class="btn btn-secondary mt-3" onclick="history.back()">Kembali</button>
                <p>Mata Pelajaran: <strong><?= htmlspecialchars($mapel); ?></strong></p>
            </div>

            <div class="page-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Nilai Siswa</h4>
                                <a href="export_excel.php?id_kelas=<?= $id_kelas; ?>&id_guru=<?= $id_guru; ?>&mapel=<?= urlencode($mapel); ?>" 
                                   class="btn btn-success">Cetak ke Excel</a>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="checkAll"></th>
                                                <th>Nama Siswa</th>
                                                <th>Nilai</th>
                                                <th>Catatan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result->num_rows > 0): ?>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><input type="checkbox" name="delete_ids[]" value="<?= $row['id_ulangan']; ?>"></td>
                                                        <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                                        <td><?= htmlspecialchars($row['nilai']); ?></td>
                                                        <td><?= htmlspecialchars($row['catatan']); ?></td>
                                                        <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5">Tidak ada data nilai untuk mata pelajaran ini.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <button type="submit" name="delete_selected" class="btn btn-danger">Hapus Terpilih</button>
                                </form>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
            include 'root/menu-mobile.php';
include 'root/footer.php'
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('checkAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>
</body>

</html>
