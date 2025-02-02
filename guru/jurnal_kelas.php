<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan jabatan sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil kode_kelas dari parameter URL
$kode_kelas = $_GET['kode_kelas'] ?? null;

if (!$kode_kelas) {
    echo "Kode kelas tidak ditemukan.";
    exit;
}

// Ambil id_kelas berdasarkan kode_kelas
$query_kelas = "SELECT id_kelas FROM kelas WHERE kode_kelas = ?";
$stmt = $mysqli->prepare($query_kelas);
$stmt->bind_param("s", $kode_kelas);
$stmt->execute();
$result_kelas = $stmt->get_result();
$kelas = $result_kelas->fetch_assoc();

if (!$kelas) {
    echo "Kode kelas tidak valid.";
    exit;
}

$id_kelas = $kelas['id_kelas'];

// Ambil id_guru dari session
$id_guru = $_SESSION['id_guru'] ?? null;

if (!$id_guru) {
    echo "ID Guru tidak ditemukan.";
    exit;
}

// Jika data disimpan ke jurnal_kelas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_jurnal'])) {
    $jam_pelajaran = $_POST['jam_pelajaran'];
    $id_pelajaran = $_POST['id_pelajaran'];
    $topik_pembahasan = $_POST['topik_pembahasan'];
    $catatan = $_POST['catatan'];
    $tanggal = date('Y-m-d'); // Tanggal otomatis

    // Ambil nama mata pelajaran berdasarkan id_pelajaran
    $query_pelajaran = "SELECT nama_pelajaran FROM pelajaran WHERE id_pelajaran = ?";
    $stmt_pelajaran = $mysqli->prepare($query_pelajaran);
    $stmt_pelajaran->bind_param("i", $id_pelajaran);
    $stmt_pelajaran->execute();
    $result_pelajaran = $stmt_pelajaran->get_result();
    $pelajaran = $result_pelajaran->fetch_assoc();

    if (!$pelajaran) {
        echo "<script>alert('Mata pelajaran tidak ditemukan!');</script>";
        exit;
    }

    $nama_pelajaran = $pelajaran['nama_pelajaran'];

    // Simpan data ke jurnal_kelas
    $query = "INSERT INTO jurnal_kelas (id_guru, id_kelas, jam_pelajaran, nama_mapel, topik_pembahasan, catatan, tanggal)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iisssss", $id_guru, $id_kelas, $jam_pelajaran, $nama_pelajaran, $topik_pembahasan, $catatan, $tanggal);

    if ($stmt->execute()) {
        echo "<script>alert('Jurnal berhasil disimpan!');</script>";
    } else {
        echo "<script>alert('Gagal menyimpan jurnal.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<body>
    <script src="assets/static/js/initTheme.js"></script>
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
        
            <div class="">
                        <!-- Button Kembali -->
                        <button class="btn btn-secondary" onclick="history.back()">Kembali</button>
                    </div><hr>
        <!-- Button Modal -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#jurnalModal">Tambah Jurnal</button>

        <!-- Modal -->
        <div class="modal fade" id="jurnalModal" tabindex="-1" aria-labelledby="jurnalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jurnalModalLabel">Tambah Jurnal Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="jam_pelajaran" class="form-label">Jam Pelajaran</label>
                                <input type="text" name="jam_pelajaran" id="jam_pelajaran" class="form-control"
                                    placeholder="Contoh: 1-2" required>
                            </div>
                            <div class="mb-3">
                                <label for="id_pelajaran" class="form-label">Mata Pelajaran</label>
                                <select name="id_pelajaran" id="id_pelajaran" class="form-select" required>
                                    <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                                    <?php
                            $query_pelajaran = "SELECT * FROM pelajaran WHERE id_kelas = ? AND id_guru = ?";
                            $stmt = $mysqli->prepare($query_pelajaran);
                            $stmt->bind_param("ii", $id_kelas, $id_guru);
                            $stmt->execute();
                            $result_pelajaran = $stmt->get_result();

                            while ($pelajaran = $result_pelajaran->fetch_assoc()) {
                                echo "<option value='" . $pelajaran['id_pelajaran'] . "'>" . htmlspecialchars($pelajaran['nama_pelajaran']) . "</option>";
                            }
                            ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="topik_pembahasan" class="form-label">Topik Pembahasan</label>
                                <textarea name="topik_pembahasan" id="topik_pembahasan" class="form-control" rows="3"
                                    required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" name="save_jurnal" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <h3>Daftar Jurnal Kelas</h3>
       <div class="table-responsive">
       <table class="table table-striped" id="table1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam Pelajaran</th>
                    <th>Mata Pelajaran</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
$query_jurnal = "SELECT * FROM jurnal_kelas WHERE id_kelas = ? AND id_guru = ? ORDER BY created_at DESC"; // Urutkan berdasarkan created_at
$stmt = $mysqli->prepare($query_jurnal);
$stmt->bind_param("ii", $id_kelas, $id_guru);
$stmt->execute();
$result_jurnal = $stmt->get_result();
$no = 1;

while ($jurnal = $result_jurnal->fetch_assoc()): ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($jurnal['tanggal']); ?></td>
        <td><?= htmlspecialchars($jurnal['jam_pelajaran']); ?></td>
        <td><?= htmlspecialchars($jurnal['nama_mapel']); ?></td>
        <td>
            <!-- Tombol untuk Membuka Modal -->
            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                data-bs-target="#detailModal-<?= $jurnal['id_jurnal']; ?>">Lihat</button>
        </td>
    </tr>

    <!-- Modal untuk Menampilkan Detail Jurnal -->
    <div class="modal fade" id="detailModal-<?= $jurnal['id_jurnal']; ?>" tabindex="-1" aria-labelledby="detailModalLabel-<?= $jurnal['id_jurnal']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel-<?= $jurnal['id_jurnal']; ?>">Detail Jurnal Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Tanggal:</strong> <?= htmlspecialchars($jurnal['tanggal']); ?></p>
                    <p><strong>Jam Pelajaran:</strong> <?= htmlspecialchars($jurnal['jam_pelajaran']); ?></p>
                    <p><strong>Mata Pelajaran:</strong> <?= htmlspecialchars($jurnal['nama_mapel']); ?></p>
                    <p><strong>Topik Pembahasan:</strong> <?= htmlspecialchars($jurnal['topik_pembahasan']); ?></p>
                    <p><strong>Catatan:</strong> <?= htmlspecialchars($jurnal['catatan']); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>

            </tbody>
        </table>
       </div>


    </div>

        </div>

    </div> <?php
            include 'root/menu-mobile.php';
include 'root/footer.php'
?>

    <?php include 'root/js.php'; ?>
</body>

</html>