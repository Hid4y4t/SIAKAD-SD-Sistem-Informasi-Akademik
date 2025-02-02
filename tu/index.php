<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);


require '../koneksi/koneksi.php'; // Pastikan jalur ke file koneksi benar


// Query jumlah total siswa
$queryJumlahSiswa = "SELECT COUNT(*) AS jumlah_siswa FROM siswa";
$resultJumlahSiswa = $mysqli->query($queryJumlahSiswa);
$jumlahSiswa = $resultJumlahSiswa->fetch_assoc()['jumlah_siswa'];

// Query jumlah siswa yang belum bayar SPP bulan ini
$currentMonth = date('m');
$currentYear = date('Y');
$queryBelumBayarSPP = "
    SELECT COUNT(*) AS jumlah_belum_bayar 
    FROM pembayaran_spp 
    WHERE status = 'belum_bayar' AND bulan = $currentMonth AND tahun = $currentYear";
$resultBelumBayarSPP = $mysqli->query($queryBelumBayarSPP);
$jumlahBelumBayarSPP = $resultBelumBayarSPP->fetch_assoc()['jumlah_belum_bayar'];

// Query jumlah siswa yang mendapat beasiswa
$queryBeasiswa = "SELECT COUNT(DISTINCT id_siswa) AS jumlah_beasiswa FROM beasiswa";
$resultBeasiswa = $mysqli->query($queryBeasiswa);
$jumlahBeasiswa = $resultBeasiswa->fetch_assoc()['jumlah_beasiswa'];

// Query jumlah siswa yang mendapat potongan SPP
$queryPotongan = "SELECT COUNT(DISTINCT id_siswa) AS jumlah_potongan FROM potongan_spp";
$resultPotongan = $mysqli->query($queryPotongan);
$jumlahPotongan = $resultPotongan->fetch_assoc()['jumlah_potongan'];

// Query untuk menampilkan siswa yang belum membayar SPP (ada di tabel `notifikasi_spp`)
$queryBelumBayar = "
    SELECT s.nis, s.nama_siswa, s.kelas, n.bulan_tagihan, n.tahun_tagihan
    FROM notifikasi_spp n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    ORDER BY n.tahun_tagihan DESC, n.bulan_tagihan DESC
";
$resultBelumBayar = $mysqli->query($queryBelumBayar);
?>


<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* Gaya sederhana untuk menyesuaikan layout */
.chart-container {
    width: 100%;
    margin: auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.chart-container h4 {
    font-family: Arial, sans-serif;
    color: #333;
}
</style>
<?php
include 'root/head.php'
?>


<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php
include 'root/menu.php'
?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Profile Statistics</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-9">


                        <div class="row">
                            <!-- Jumlah Siswa -->
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div
                                                class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon blue mb-2">
                                                    <i class="iconly-boldProfile"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Jumlah Siswa</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $jumlahSiswa; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Jumlah Siswa yang Mendapat Beasiswa -->
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div
                                                class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon green mb-2">
                                                    <i class="iconly-boldAdd-User"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Beasiswa</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $jumlahBeasiswa; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Jumlah Siswa yang Mendapat Potongan SPP -->
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div
                                                class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon orange mb-2">
                                                    <i class="iconly-boldDiscount"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Potongan SPP</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $jumlahPotongan; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <section class="row">
                                    <div class="col-12 col-lg-12">

                                        <!-- Tabel Siswa Belum Bayar SPP -->
                                        <section class="section">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title">Siswa Belum Bayar SPP</h5>
                                                    <a href="print_belum_bayar_spp.php"
                                                        class="btn btn-warning">Print</a>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Kolom Pencarian untuk Tabel Belum Bayar -->
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="searchBelumBayar" class="form-control"
                                                            placeholder="Cari Siswa (NIS, Nama, atau Kelas)">
                                                    </div>
                                                    <div class="table-responsive datatable-minimal">
                                                        <table class="table" id="tableBelumBayar">
                                                            <thead>
                                                                <tr>
                                                                    <th>NIS</th>
                                                                    <th>Nama</th>
                                                                    <th>Kelas</th>
                                                                    <th>Bulan Tagihan</th>
                                                                    <th>Tahun Tagihan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php while ($row = $resultBelumBayar->fetch_assoc()): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($row['nis']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['kelas']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['bulan_tagihan']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['tahun_tagihan']); ?>
                                                                    </td>
                                                                </tr>
                                                                <?php endwhile; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>


                                    </div>
                                </section>
                            </div>
                        </div>

                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div class="card-body py-4 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xl">
                                        <img src="../assets/compiled/jpg/1.jpg" alt="Face 1">
                                    </div>
                                    <div class="ms-3 name">
                                        <h5 class="font-bold">Admin TU</h5>
                                        <!-- <h6 class="text-muted mb-0">@Hidayat</h6> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="chart-container">
                                <h4>Data Siswa Berdasarkan Jenis Kelamin</h4>
                                <canvas id="siswaChart" width="400" height="400"></canvas>
                            </div>
                    </div>
                </section>
            </div>


            <?php
include 'root/footer.php'
?>




        </div>
    </div>

    <?php
include 'root/js.php'
?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Mengambil data dari endpoint PHP
        fetch('get_siswa_data2.php')
            .then(response => response.json())
            .then(data => {
                // Ambil konteks dari elemen canvas
                const ctx = document.getElementById('siswaChart').getContext('2d');

                // Membuat Doughnut Chart menggunakan data dari server
                new Chart(ctx, {
                    type: 'doughnut', // Menggunakan chart tipe doughnut
                    data: {
                        labels: ['Male', 'Female'],
                        datasets: [{
                            data: [data.male, data.female],
                            backgroundColor: ['#4F6DFF',
                            '#53C1E9'], // Warna masing-masing bagian
                            hoverBackgroundColor: ['#3A53B4',
                            '#38A0C0'], // Warna saat di hover
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    });

    // Fungsi Pencarian untuk Tabel Belum Bayar
    document.getElementById('searchBelumBayar').addEventListener('keyup', function() {
        var searchValue = this.value.toLowerCase();
        var rows = document.querySelectorAll('#tableBelumBayar tbody tr');

        rows.forEach(function(row) {
            var nis = row.cells[0].textContent.toLowerCase();
            var nama = row.cells[1].textContent.toLowerCase();
            var kelas = row.cells[2].textContent.toLowerCase();

            if (nis.includes(searchValue) || nama.includes(searchValue) || kelas.includes(
                searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>

</body>

</html>