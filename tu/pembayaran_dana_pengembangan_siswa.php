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

// Mendapatkan id_dana_pengembangan berdasarkan id_siswa
$queryDanaPengembangan = "SELECT id_dana_pengembangan, total_tagihan, jumlah_terbayar FROM dana_pengembangan WHERE id_siswa = ?";
$stmtDanaPengembangan = $mysqli->prepare($queryDanaPengembangan);
$stmtDanaPengembangan->bind_param("i", $id_siswa);
$stmtDanaPengembangan->execute();
$dataDanaPengembangan = $stmtDanaPengembangan->get_result()->fetch_assoc();

if (!$dataDanaPengembangan) {
    echo "Data Dana Pengembangan untuk siswa ini tidak ditemukan.";
    exit;
}

$id_dana_pengembangan = $dataDanaPengembangan['id_dana_pengembangan'];
$jumlahTagihan = $dataDanaPengembangan['total_tagihan'];
$totalBayar = $dataDanaPengembangan['jumlah_terbayar'];

// Menghitung sisa tagihan
$sisaTagihan = $jumlahTagihan - $totalBayar;
// Memproses form cicilan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['jumlah_cicilan'])) {
    $cicilan = floatval($_POST['jumlah_cicilan']);
    if ($cicilan > 0 && $cicilan <= $sisaTagihan) {
        // Insert cicilan ke dana_pengembangan_history
        $insertCicilan = "INSERT INTO dana_pengembangan_history (id_dana_pengembangan, jumlah_bayar, tanggal_bayar) VALUES (?, ?, NOW())";
        $stmtCicilan = $mysqli->prepare($insertCicilan);
        $stmtCicilan->bind_param("id", $id_dana_pengembangan, $cicilan);
        $stmtCicilan->execute();

        // Update jumlah terbayar pada tabel dana_pengembangan
        $updateDanaPengembangan = "UPDATE dana_pengembangan SET jumlah_terbayar = jumlah_terbayar + ? WHERE id_dana_pengembangan = ?";
        $stmtUpdate = $mysqli->prepare($updateDanaPengembangan);
        $stmtUpdate->bind_param("di", $cicilan, $id_dana_pengembangan);
        $stmtUpdate->execute();

        // Memeriksa apakah total terbayar sama dengan atau lebih dari total tagihan
        $queryCheckStatus = "SELECT total_tagihan, jumlah_terbayar FROM dana_pengembangan WHERE id_dana_pengembangan = ?";
        $stmtCheckStatus = $mysqli->prepare($queryCheckStatus);
        $stmtCheckStatus->bind_param("i", $id_dana_pengembangan);
        $stmtCheckStatus->execute();
        $resultCheckStatus = $stmtCheckStatus->get_result()->fetch_assoc();

        if ($resultCheckStatus['jumlah_terbayar'] >= $resultCheckStatus['total_tagihan']) {
            // Perbarui status menjadi 'Lunas' jika jumlah terbayar sudah memenuhi total tagihan
            $updateStatus = "UPDATE dana_pengembangan SET status = 'Lunas' WHERE id_dana_pengembangan = ?";
            $stmtUpdateStatus = $mysqli->prepare($updateStatus);
            $stmtUpdateStatus->bind_param("i", $id_dana_pengembangan);
            $stmtUpdateStatus->execute();
        }

        // Alihkan ke halaman pembayaran_dana_pengembangan.php setelah pembayaran berhasil
        header("Location: pembayaran_dana_pengembangan.php");
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
                <h3>Bayar Dana Pengembangan</h3>
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
