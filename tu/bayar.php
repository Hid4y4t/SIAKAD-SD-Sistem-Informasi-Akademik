<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';
echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Menangkap `id_siswa` dari URL atau dari form submission
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : (isset($_POST['id_siswa']) ? intval($_POST['id_siswa']) : 0);

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

// Mendapatkan angkatan siswa untuk menghitung tagihan
$angkatan = $siswaData['angkatan'];

// Mengambil jumlah tagihan bulanan sesuai angkatan
$tagihanQuery = "SELECT jumlah_tagihan FROM tagihan_spp WHERE angkatan = ?";
$stmtTagihan = $mysqli->prepare($tagihanQuery);
$stmtTagihan->bind_param("i", $angkatan);
$stmtTagihan->execute();
$jumlahTagihan = $stmtTagihan->get_result()->fetch_assoc()['jumlah_tagihan'] ?? 0;

// Mengambil daftar notifikasi SPP berdasarkan `id_siswa`
$notifikasiQuery = "SELECT bulan_tagihan, tahun_tagihan FROM notifikasi_spp WHERE id_siswa = ?";
$stmtNotifikasi = $mysqli->prepare($notifikasiQuery);
$stmtNotifikasi->bind_param("i", $id_siswa);
$stmtNotifikasi->execute();
$notifikasiResult = $stmtNotifikasi->get_result();

// Mengambil daftar potongan (beasiswa dan potongan_spp) untuk siswa berdasarkan `id_siswa`
$potonganQuery = "
    SELECT b.jumlah AS beasiswa, ps.jumlah AS potongan_spp
    FROM beasiswa b
    LEFT JOIN potongan_spp ps ON ps.id_siswa = b.id_siswa
    WHERE b.id_siswa = ?
";
$stmtPotongan = $mysqli->prepare($potonganQuery);
$stmtPotongan->bind_param("i", $id_siswa);
$stmtPotongan->execute();
$potonganData = $stmtPotongan->get_result()->fetch_assoc();

// Menghitung total potongan
$totalPotongan = ($potonganData['beasiswa'] ?? 0) + ($potonganData['potongan_spp'] ?? 0);

// Memproses form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $selectedMonths = $_POST['pembayaran']; // Array bulan-tahun dalam format "bulan-tahun"
    $keterangan = $_POST['keterangan'] ?? '';

    if (!empty($selectedMonths)) {
        // Mulai transaksi
        $mysqli->begin_transaction();

        try {
            // Hitung total tagihan berdasarkan jumlah bulan yang dipilih
            $totalTagihan = count($selectedMonths) * $jumlahTagihan;
            $totalBayar = $totalTagihan - $totalPotongan;

            // Siapkan data untuk tabel `nota`
            $jenis_pembayaran = 'SPP';
            $jenis_potongan = null;
            if ($totalPotongan > 0) {
                $jenis_potongan = 'Beasiswa/Potongan SPP';
            }
            $tanggal_pembayaran = date('Y-m-d'); // Tanggal hari ini

            // Insert into `nota`
            $notaQuery = "INSERT INTO nota (id_siswa, jenis_pembayaran, jenis_potongan, jumlah_dibayarkan, keterangan, tanggal_pembayaran) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtNota = $mysqli->prepare($notaQuery);
            $stmtNota->bind_param("issdss", $id_siswa, $jenis_pembayaran, $jenis_potongan, $totalBayar, $keterangan, $tanggal_pembayaran);
            $stmtNota->execute();
            $id_nota = $stmtNota->insert_id;

            // Proses setiap bulan yang dipilih
            foreach ($selectedMonths as $monthYear) {
                // Pisahkan bulan dan tahun
                list($bulan_tagihan, $tahun_tagihan) = explode('-', $monthYear);

                // Insert into `pembayaran_spp`
                $pembayaranQuery = "INSERT INTO pembayaran_spp (id_siswa, bulan, tahun, jumlah, tanggal_bayar, status) VALUES (?, ?, ?, ?, ?, 'sudah_bayar')";
                $stmtPembayaran = $mysqli->prepare($pembayaranQuery);
                $stmtPembayaran->bind_param("iiids", $id_siswa, $bulan_tagihan, $tahun_tagihan, $jumlahTagihan, $tanggal_pembayaran);
                $stmtPembayaran->execute();

                // Hapus dari `notifikasi_spp`
                $deleteNotifikasiQuery = "DELETE FROM notifikasi_spp WHERE id_siswa = ? AND bulan_tagihan = ? AND tahun_tagihan = ?";
                $stmtDeleteNotifikasi = $mysqli->prepare($deleteNotifikasiQuery);
                $stmtDeleteNotifikasi->bind_param("iii", $id_siswa, $bulan_tagihan, $tahun_tagihan);
                $stmtDeleteNotifikasi->execute();
            }

            // Commit transaksi
            $mysqli->commit();

            // Pesan sukses dan redirect
            echo "<script>alert('Pembayaran berhasil diproses.'); window.location.href = 'bayarspp.php';</script>";
            exit;

        } catch (Exception $e) {
            // Rollback jika terjadi kesalahan
            $mysqli->rollback();
            echo "Terjadi kesalahan: " . $e->getMessage();
        }
    } else {
        echo "Silakan pilih setidaknya satu bulan untuk dibayar.";
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
                <h3>Bayar SPP</h3>
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
                                                                <label>TTL</label>
                                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($siswaData['tanggal_lahir']); ?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Daftar Pembayaran SPP -->
                                                    <hr>
                                                    <div class="row">
                                                        <div class="form-group col-6">
                                                            <h5>List Pembayaran SPP</h5>
                                                            <?php
                                                            $totalTagihan = 0;
                                                            $notifikasiResult->data_seek(0); // Reset pointer result set
                                                            while ($notifikasi = $notifikasiResult->fetch_assoc()):
                                                                $totalTagihan += $jumlahTagihan;
                                                                $bulanTagihan = $notifikasi['bulan_tagihan'];
                                                                $tahunTagihan = $notifikasi['tahun_tagihan'];
                                                                $monthYearValue = $bulanTagihan . '-' . $tahunTagihan;
                                                            ?>
                                                            <div class="checkbox">
                                                                <input type="checkbox" class="form-check-input pembayaran-checkbox" name="pembayaran[]" value="<?php echo $monthYearValue; ?>" data-amount="<?php echo $jumlahTagihan; ?>" checked onchange="updateTotal()">
                                                                <label>Bayar SPP Bulan <?php echo htmlspecialchars($bulanTagihan) . ' Tahun ' . htmlspecialchars($tahunTagihan); ?> - Rp <?php echo number_format($jumlahTagihan, 0, ',', '.'); ?></label>
                                                            </div>
                                                            <?php endwhile; ?>
                                                        </div>

                                                        <!-- Daftar Potongan SPP -->
                                                        <div class="form-group col-6">
                                                            <h5>List Potongan SPP</h5>
                                                            <ul>
                                                                <?php if (isset($potonganData['beasiswa']) && $potonganData['beasiswa'] > 0): ?>
                                                                <li>Beasiswa: Rp <?php echo number_format($potonganData['beasiswa'], 0, ',', '.'); ?></li>
                                                                <?php endif; ?>
                                                                <?php if (isset($potonganData['potongan_spp']) && $potonganData['potongan_spp'] > 0): ?>
                                                                <li>Potongan SPP: Rp <?php echo number_format($potonganData['potongan_spp'], 0, ',', '.'); ?></li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <!-- Total Bayar -->
                                                    <hr>
                                                    <div class="page-heading">
                                                        <h5>Total Yang Harus Dibayarkan</h5>
                                                        <h3 id="totalBayar">Rp <?php echo number_format($totalTagihan - $totalPotongan, 0, ',', '.'); ?></h3>
                                                    </div>
                                                    <!-- Keterangan Tambahan -->
                                                    <div class="col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="keterangan">Tambah Keterangan</label>
                                                            <input type="text" id="keterangan" class="form-control" name="keterangan" placeholder="Keterangan ">
                                                        </div>
                                                    </div>

                                                    <!-- Tombol Submit -->
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
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
    function updateTotal() {
        const checkboxes = document.querySelectorAll('.pembayaran-checkbox');
        let totalBayar = <?php echo $totalPotongan ? '-' . $totalPotongan : '0'; ?>;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                totalBayar += parseFloat(checkbox.getAttribute('data-amount'));
            }
        });

        const formattedTotal = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(totalBayar);
        document.getElementById('totalBayar').innerText = formattedTotal;
    }
    </script>

    <?php include 'root/js.php'; ?>
</body>

</html>
