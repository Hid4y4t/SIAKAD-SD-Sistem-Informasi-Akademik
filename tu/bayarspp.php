<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Fungsi untuk menambahkan notifikasi otomatis bagi siswa yang belum membayar SPP bulan ini
$currentMonth = date('n'); // Bulan saat ini dalam angka (1-12)
$currentYear = date('Y');  // Tahun saat ini

// Query untuk mendapatkan siswa yang belum membayar SPP pada bulan ini
$query = "
    SELECT s.id_siswa 
    FROM siswa s
    LEFT JOIN pembayaran_spp p ON s.id_siswa = p.id_siswa AND p.bulan = ? AND p.tahun = ?
    WHERE p.id_pembayaran IS NULL
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $currentMonth, $currentYear);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $id_siswa = $row['id_siswa'];

    // Cek apakah notifikasi sudah ada untuk siswa dan bulan ini
    $checkQuery = "
        SELECT * FROM notifikasi_spp 
        WHERE id_siswa = ? AND bulan_tagihan = ? AND tahun_tagihan = ?
    ";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("iii", $id_siswa, $currentMonth, $currentYear);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        // Tambahkan notifikasi jika belum ada
        $pesan = "SPP bulan $currentMonth $currentYear belum dibayar.";
        $insertQuery = "
            INSERT INTO notifikasi_spp (id_siswa, bulan_tagihan, tahun_tagihan, pesan) 
            VALUES (?, ?, ?, ?)
        ";
        $insertStmt = $mysqli->prepare($insertQuery);
        $insertStmt->bind_param("iiis", $id_siswa, $currentMonth, $currentYear, $pesan);
        $insertStmt->execute();
    }
}

// Menangani form input NIS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis']);
    
    // Cek apakah NIS ada di tabel siswa
    $query = "SELECT id_siswa FROM siswa WHERE nis = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_siswa);
        $stmt->fetch();
        header("Location: bayar.php?id_siswa=" . $id_siswa);
        exit;
    } else {
        $error_message = "NIS tidak ditemukan. Silakan periksa kembali.";
    }
    $stmt->close();
}

// Mengambil 10 data terakhir dari tabel `nota`
$queryPembayaranTerakhir = "
    SELECT s.nama_siswa, s.kelas, n.tanggal_pembayaran, n.id_nota
    FROM nota n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    ORDER BY n.tanggal_pembayaran DESC
    LIMIT 10
";
$resultPembayaranTerakhir = $mysqli->query($queryPembayaranTerakhir);

if (!$resultPembayaranTerakhir) {
    die("Query Error: " . $mysqli->error);
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
                <h3>Bayar SPP</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="col-sm-10">
                            <form action="" method="POST">
                                <div class="input-group mb-3">
                                    <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS Siswa"
                                        aria-label="Masukkan NIS Siswa" required>
                                    <button class="btn btn-primary" type="submit">Bayar</button>
                                </div>
                                <?php if (isset($error_message)): ?>
                                    <div class="alert alert-danger">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>

                        <!-- Tabel Data Pembayaran Terakhir -->
                        <section class="section">
                            <div class="row" id="table-borderless">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Data Terakhir Pembayaran</h4>
                                        </div>
                                        <div class="card-content">
                                            <!-- Table with no border -->
                                            <div class="table-responsive">
                                                <table class="table table-borderless mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama</th>
                                                            <th>Kelas</th>
                                                            <th>Tanggal Bayar</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while ($row = $resultPembayaranTerakhir->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_pembayaran'])); ?></td>
                                                            <td>
                                                                <!-- Tombol Cetak -->
                                                                <a href="cetak_nota.php?id_nota=<?php echo $row['id_nota']; ?>" target="_blank" class="btn btn-info btn-sm">Cetak</a>
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
                        </section>

                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include 'root/js.php'; ?>
</body>
</html>
