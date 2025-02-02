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

// Mengambil daftar buku yang masih memiliki tagihan untuk siswa
$queryBuku = "SELECT bp.id_pembayaran, b.jenis_buku, bp.total_tagihan, bp.jumlah_terbayar 
              FROM buku_pembayaran bp 
              JOIN buku b ON bp.id_buku = b.id_buku 
              WHERE bp.id_siswa = ? AND bp.status = 'Belum Lunas'";
$stmtBuku = $mysqli->prepare($queryBuku);
$stmtBuku->bind_param("i", $id_siswa);
$stmtBuku->execute();
$resultBuku = $stmtBuku->get_result();

$daftarBuku = [];
while ($row = $resultBuku->fetch_assoc()) {
    $row['sisa_tagihan'] = $row['total_tagihan'] - $row['jumlah_terbayar'];
    $daftarBuku[] = $row;
}

// Memproses form cicilan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['jumlah_cicilan'], $_POST['id_pembayaran'])) {
    $id_pembayaran = intval($_POST['id_pembayaran']);
    $cicilan = floatval($_POST['jumlah_cicilan']);

    // Validasi cicilan sesuai dengan sisa tagihan untuk buku yang dipilih
    $queryTagihan = "SELECT total_tagihan, jumlah_terbayar FROM buku_pembayaran WHERE id_pembayaran = ?";
    $stmtTagihan = $mysqli->prepare($queryTagihan);
    $stmtTagihan->bind_param("i", $id_pembayaran);
    $stmtTagihan->execute();
    $tagihanData = $stmtTagihan->get_result()->fetch_assoc();
    $sisaTagihan = $tagihanData['total_tagihan'] - $tagihanData['jumlah_terbayar'];

    if ($cicilan > 0 && $cicilan <= $sisaTagihan) {
        // Insert cicilan ke buku_history
        $insertCicilan = "INSERT INTO buku_history (id_pembayaran, jumlah_bayar, tanggal_bayar) VALUES (?, ?, NOW())";
        $stmtCicilan = $mysqli->prepare($insertCicilan);
        $stmtCicilan->bind_param("id", $id_pembayaran, $cicilan);
        $stmtCicilan->execute();

        // Update jumlah terbayar pada tabel buku_pembayaran
        $updatePembayaran = "UPDATE buku_pembayaran SET jumlah_terbayar = jumlah_terbayar + ? WHERE id_pembayaran = ?";
        $stmtUpdate = $mysqli->prepare($updatePembayaran);
        $stmtUpdate->bind_param("di", $cicilan, $id_pembayaran);
        $stmtUpdate->execute();

        // Cek apakah pembayaran sudah lunas
        $queryCheckStatus = "SELECT total_tagihan, jumlah_terbayar FROM buku_pembayaran WHERE id_pembayaran = ?";
        $stmtCheckStatus = $mysqli->prepare($queryCheckStatus);
        $stmtCheckStatus->bind_param("i", $id_pembayaran);
        $stmtCheckStatus->execute();
        $resultCheckStatus = $stmtCheckStatus->get_result()->fetch_assoc();

        if ($resultCheckStatus['jumlah_terbayar'] >= $resultCheckStatus['total_tagihan']) {
            // Perbarui status menjadi 'Lunas'
            $updateStatus = "UPDATE buku_pembayaran SET status = 'Lunas' WHERE id_pembayaran = ?";
            $stmtUpdateStatus = $mysqli->prepare($updateStatus);
            $stmtUpdateStatus->bind_param("i", $id_pembayaran);
            $stmtUpdateStatus->execute();
        }

        header("Location: pembayaran_buku.php?id_siswa=$id_siswa");
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
                <h3>Pembayaran Buku</h3>
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

                                                    <!-- Pilih Buku -->
                                                    <hr>
                                                    <div class="form-group">
                                                        <label for="id_pembayaran">Pilih Buku</label>
                                                        <select class="form-control" name="id_pembayaran" id="id_pembayaran" onchange="updateTagihan()">
                                                            <option value="">Pilih Buku untuk Dibayar</option>
                                                            <?php foreach ($daftarBuku as $buku): ?>
                                                                <option value="<?php echo $buku['id_pembayaran']; ?>" data-tagihan="<?php echo $buku['sisa_tagihan']; ?>">
                                                                    <?php echo htmlspecialchars($buku['jenis_buku']); ?> - Sisa Tagihan: Rp <?php echo number_format($buku['sisa_tagihan'], 0, ',', '.'); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <!-- Informasi Cicilan -->
                                                    <div class="form-group mt-3">
                                                        <label for="jumlah_cicilan">Masukkan Cicilan</label>
                                                        <input type="number" id="jumlah_cicilan" class="form-control" name="jumlah_cicilan" placeholder="Masukkan jumlah cicilan" oninput="validateCicilan()" min="0">
                                                    </div>

                                                    <!-- Tombol Submit -->
                                                    <div class="col-12 d-flex justify-content-end mt-3">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1" onclick="resetForm()">Reset</button>
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
    function updateTagihan() {
        const selectedOption = document.getElementById('id_pembayaran').selectedOptions[0];
        const sisaTagihan = selectedOption ? selectedOption.getAttribute('data-tagihan') : 0;
        document.getElementById('jumlah_cicilan').max = sisaTagihan;
        validateCicilan();
    }

    function validateCicilan() {
        const maxCicilan = document.getElementById('jumlah_cicilan').max;
        const cicilanInput = parseFloat(document.getElementById('jumlah_cicilan').value) || 0;
        if (cicilanInput > maxCicilan) {
            alert("Jumlah cicilan melebihi sisa tagihan buku yang dipilih.");
            document.getElementById('jumlah_cicilan').value = maxCicilan;
        }
    }

    function resetForm() {
        document.getElementById('jumlah_cicilan').value = '';
        document.getElementById('id_pembayaran').selectedIndex = 0;
    }
    </script>

    <?php include 'root/js.php'; ?>
</body>
</html>
