<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil parameter kelas dari URL
if (!isset($_GET['kelas'])) {
    header("Location: siswa.php");
    exit;
}
$kelas = urldecode($_GET['kelas']);

// Ambil data siswa berdasarkan kelas
$query_siswa = "SELECT * FROM siswa WHERE kelas = ?";
$stmt_siswa = $mysqli->prepare($query_siswa);
$stmt_siswa->bind_param("s", $kelas);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();

// Ambil daftar kelas untuk dropdown
$query_daftar_kelas = "SELECT DISTINCT nama_kelas AS kelas FROM kelas ORDER BY nama_kelas ASC";
$result_daftar_kelas = $mysqli->query($query_daftar_kelas);
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
                <h3>Detail Siswa - Kelas <?= htmlspecialchars($kelas); ?></h3>
            </div>

            <div class="page-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Siswa</h4>
                                
                            </div>
                            <div class="card-body">
                                <form id="edit-kelas-form" method="POST" action="proses_edit_kelas2.php">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select-all"></th>
                                                <th>No</th>
                                                <th>Nama Siswa</th>
                                                <th>Email</th>
                                                <th>Telepon</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; ?>
                                            <?php while ($siswa = $result_siswa->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_siswa[]" value="<?= $siswa['id_siswa']; ?>">
                                                </td>
                                                <td><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($siswa['nama_siswa']); ?></td>
                                                <td><?= htmlspecialchars($siswa['email_siswa']); ?></td>
                                                <td><?= htmlspecialchars($siswa['telepon_siswa']); ?></td>
                                               
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <div class="mt-3">
                                        <select name="kelas_baru" class="form-select" required>
                                            <option value="" disabled selected>Pilih Kelas Baru</option>
                                            <?php while ($kelas_baru = $result_daftar_kelas->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($kelas_baru['kelas']); ?>">
                                                <?= htmlspecialchars($kelas_baru['kelas']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <button type="submit" class="btn btn-success mt-2">Ganti Kelas</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'root/footer.php'; ?>
        </div>
    </div>

    <script>
        // Select all checkboxes
        document.getElementById('select-all').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_siswa[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>
</body>
</html>
