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

// Mengambil informasi transportasi berdasarkan id_siswa
$queryTransportasi = "SELECT tp.id_pembayaran, z.harga_per_trip, z.nama_zona 
                      FROM transportasi_pembayaran tp
                      JOIN zona_transportasi z ON tp.id_zona = z.id_zona
                      WHERE tp.id_siswa = ?";
$stmtTransportasi = $mysqli->prepare($queryTransportasi);
$stmtTransportasi->bind_param("i", $id_siswa);
$stmtTransportasi->execute();
$dataTransportasi = $stmtTransportasi->get_result()->fetch_assoc();

if (!$dataTransportasi) {
    echo "Data transportasi untuk siswa ini tidak ditemukan.";
    exit;
}

$id_pembayaran = $dataTransportasi['id_pembayaran'];
$hargaPerTrip = $dataTransportasi['harga_per_trip'];
$namaZona = $dataTransportasi['nama_zona'];

// Memproses form pembayaran per trip
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['total_trip'])) {
    $totalTrip = intval($_POST['total_trip']);
    $jumlahBayar = $totalTrip * $hargaPerTrip;

    if ($totalTrip > 0) {
        // Masukkan pembayaran ke transportasi_history
        $insertPembayaran = "INSERT INTO transportasi_history (id_pembayaran, jumlah_bayar, tanggal_bayar, total_trip) 
                             VALUES (?, ?, NOW(), ?)";
        $stmtPembayaran = $mysqli->prepare($insertPembayaran);
        $stmtPembayaran->bind_param("idi", $id_pembayaran, $jumlahBayar, $totalTrip);
        $stmtPembayaran->execute();

        // Redirect ke halaman pembayaran_transportasi.php setelah pembayaran berhasil
        header("Location: pembayaran_transportasi.php");
        exit;
    } else {
        echo "<script>alert('Jumlah trip tidak valid.');</script>";
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
                <h3>Pembayaran Transportasi</h3>
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
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label>Zona Transportasi</label>
                                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($namaZona); ?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Informasi Pembayaran Per Trip -->
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5>Harga per Trip: Rp <?php echo number_format($hargaPerTrip, 0, ',', '.'); ?></h5>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label for="total_trip">Jumlah Trip</label>
                                                        <input type="number" id="total_trip" class="form-control" name="total_trip" placeholder="Masukkan jumlah trip" min="1" oninput="updateJumlahBayar()">
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label>Total Pembayaran</label>
                                                        <h5 id="jumlahBayar">Rp 0</h5>
                                                    </div>

                                                    <!-- Tombol Submit -->
                                                    <div class="col-12 d-flex justify-content-end mt-3">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1" onclick="resetJumlahBayar()">Reset</button>
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
    const hargaPerTrip = <?php echo $hargaPerTrip; ?>;

    function updateJumlahBayar() {
        const totalTrip = document.getElementById('total_trip').value;
        const jumlahBayar = totalTrip * hargaPerTrip;
        document.getElementById('jumlahBayar').innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(jumlahBayar);
    }

    function resetJumlahBayar() {
        document.getElementById('jumlahBayar').innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(0);
    }
    </script>

    <?php include 'root/js.php'; ?>
</body>
</html>
