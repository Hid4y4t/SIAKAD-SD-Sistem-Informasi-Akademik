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

$id_kelas = $kelas['id_kelas'];
$nama_kelas = $kelas['nama_kelas'];

// Ambil data catatan siswa berdasarkan kelas dan wali kelas yang login
$query_catatan = "
    SELECT DISTINCT s.id_siswa, s.nama_siswa, g.nama_guru 
    FROM catatan_siswa cs
    JOIN siswa s ON cs.id_siswa = s.id_siswa
    JOIN kelas k ON s.kelas = k.nama_kelas
    JOIN guru g ON cs.id_guru = g.id_guru
    WHERE k.id_kelas = ? AND k.wali_kelas = ?
";
$stmt_catatan = $mysqli->prepare($query_catatan);
$stmt_catatan->bind_param("ii", $id_kelas, $id_guru);
$stmt_catatan->execute();
$result_catatan = $stmt_catatan->get_result();
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
                <h3>Catatan Guru - <?= htmlspecialchars($nama_kelas); ?></h3>
            </div>

            <div class="page-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Catatan Siswa</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Nama Guru</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php while ($catatan = $result_catatan->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($catatan['nama_siswa']); ?></td>
                                            <td><?= htmlspecialchars($catatan['nama_guru']); ?></td>
                                            <td>
                                                <button class="btn btn-info btn-sm"
                                                    onclick="openModal(<?= $catatan['id_siswa']; ?>, '<?= htmlspecialchars($catatan['nama_siswa']); ?>')">
                                                    Lihat Catatan
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modalCatatan" tabindex="-1" aria-labelledby="modalCatatanLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCatatanLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Catatan Guru</th>
                                    </tr>
                                </thead>
                                <tbody id="modalTableBody"></tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal(idSiswa, namaSiswa) {
            document.getElementById('modalCatatanLabel').textContent = `Catatan untuk ${namaSiswa}`;
            fetch(`get_catatan.php?id_siswa=${idSiswa}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('modalTableBody');
                    tableBody.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const row = `
                                <tr>
                                    <td>${item.tanggal}</td>
                                    <td>${item.catatan_guru}</td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="2">Tidak ada catatan.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            const modal = new bootstrap.Modal(document.getElementById('modalCatatan'));
            modal.show();
        }
    </script>
</body>

</html>
