<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';
echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Menangkap `id_siswa` dari URL
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;

// Validasi `id_siswa` untuk memastikan id valid
if ($id_siswa <= 0) {
    echo "ID Siswa tidak valid.";
    exit;
}

// Mengambil data siswa berdasarkan `id_siswa`
$siswaQuery = "SELECT * FROM siswa WHERE id_siswa = ?";
$stmt = $mysqli->prepare($siswaQuery);
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$siswaData = $stmt->get_result()->fetch_assoc();

if (!$siswaData) {
    echo "Siswa tidak ditemukan.";
    exit;
}

// Mendapatkan id_dana_sharing berdasarkan id_siswa
$queryDanaSharing = "SELECT id_dana_sharing, jumlah_tagihan FROM dana_sharing WHERE id_siswa = ?";
$stmtDanaSharing = $mysqli->prepare($queryDanaSharing);
$stmtDanaSharing->bind_param("i", $id_siswa);
$stmtDanaSharing->execute();
$dataDanaSharing = $stmtDanaSharing->get_result()->fetch_assoc();

if (!$dataDanaSharing) {
    echo "Data Dana Sharing untuk siswa ini tidak ditemukan. Pastikan data sudah tersedia di tabel dana_sharing.";
    exit;
}
$id_dana_sharing = $dataDanaSharing['id_dana_sharing'];
$jumlahTagihan = $dataDanaSharing['jumlah_tagihan'];

// Menghitung jumlah yang sudah dibayarkan
$queryTotalBayar = "SELECT SUM(jumlah_bayar) AS total_bayar FROM dana_sharing_history WHERE id_dana_sharing = ?";
$stmtTotalBayar = $mysqli->prepare($queryTotalBayar);
$stmtTotalBayar->bind_param("i", $id_dana_sharing);
$stmtTotalBayar->execute();
$totalBayar = $stmtTotalBayar->get_result()->fetch_assoc()['total_bayar'] ?? 0;

// Menghitung sisa tagihan
$sisaTagihan = $jumlahTagihan - $totalBayar;

// Memproses form cicilan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['jumlah_cicilan'])) {
    $cicilan = floatval($_POST['jumlah_cicilan']);
    if ($cicilan > 0 && $cicilan <= $sisaTagihan) {
        $insertCicilan = "INSERT INTO dana_sharing_history (id_dana_sharing, id_siswa, jumlah_bayar, tanggal_bayar) VALUES (?, ?, ?, NOW())";
        $stmtCicilan = $mysqli->prepare($insertCicilan);
        $stmtCicilan->bind_param("iid", $id_dana_sharing, $id_siswa, $cicilan);
        $stmtCicilan->execute();

        // Alihkan ke halaman pembayaran_dana_sharing.php setelah pembayaran berhasil
        header("Location: pembayaran_dana_sharing.php");
        exit;
    } else {
        echo "<script>alert('Jumlah cicilan tidak valid.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>

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
                <h3>Bayar Dana Sharing</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <section id="multiple-column-form">
                            <div class="row match-height">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <form class="form" method="POST" action="">
                                                    <!-- Informasi Siswa -->
                                                    <input type="hidden" name="id_siswa" value="<?php echo $id_siswa; ?>">
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label>NIS</label>
                                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($siswaData['nis']); ?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label>Nama</label>
                                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($siswaData['nama_siswa']); ?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label>Kelas</label>
                                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($siswaData['kelas']); ?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Informasi Tagihan dan Cicilan -->
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5>Jumlah Tagihan: Rp <?php echo number_format($jumlahTagihan, 0, ',', '.'); ?></h5>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h5>Sisa Tagihan: Rp <span id="sisaTagihan"><?php echo number_format($sisaTagihan, 0, ',', '.'); ?></span></h5>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label for="jumlah_cicilan">Masukkan Cicilan</label>
                                                        <input type="number" id="jumlah_cicilan" class="form-control" name="jumlah_cicilan" placeholder="Masukkan jumlah cicilan" oninput="updateSisaTagihan()" min="0" max="<?php echo $sisaTagihan; ?>">
                                                    </div>

                                                    <!-- Tombol Submit -->
                                                    <div class="col-12 d-flex justify-content-end mt-3">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1" onclick="resetSisaTagihan()">Reset</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
    const originalSisaTagihan = <?php echo $sisaTagihan; ?>;

    function updateSisaTagihan() {
        const cicilanInput = document.getElementById('jumlah_cicilan').value;
        const updatedSisa = originalSisaTagihan - parseFloat(cicilanInput || 0);
        document.getElementById('sisaTagihan').innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(updatedSisa);
    }

    function resetSisaTagihan() {
        document.getElementById('sisaTagihan').innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(originalSisaTagihan);
    }
    </script>

    <?php include 'root/js.php'; ?>
</body>
</html>
