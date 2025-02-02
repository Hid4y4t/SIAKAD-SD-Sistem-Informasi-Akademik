<?php

require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan apakah jabatan mereka sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil informasi guru dari session
$id_guru = $_SESSION['id_guru'];


// Ambil data guru dari database
$query = "SELECT * FROM guru WHERE id_guru = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_guru);
$stmt->execute();
$result = $stmt->get_result();
$guru = $result->fetch_assoc();




// Query untuk menghitung jumlah kehadiran per bulan untuk guru yang sedang login
$query = "
    SELECT MONTH(tanggal) AS bulan, COUNT(*) AS jumlah_hadir
    FROM absensi_guru
    WHERE id_guru = ? AND status = 'Hadir'
    GROUP BY MONTH(tanggal)
    ORDER BY MONTH(tanggal)
";
$stmt = executeQuery($query, [$id_guru], "i");
$result = $stmt->get_result();

// Siapkan data untuk diagram
$bulan = [];
$jumlah_hadir = [];

while ($row = $result->fetch_assoc()) {
    $bulan[] = date('F', mktime(0, 0, 0, $row['bulan'], 10)); // Mengubah angka bulan menjadi nama bulan
    $jumlah_hadir[] = $row['jumlah_hadir'];
}
?>

<!DOCTYPE html>
<html lang="en">

<?php
include 'root/head.php'
?>


<body>
    <script src="../assets/static/js/initTheme.js"></script>
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

                           
                        </div>

                        <?php



$id_guru = $_SESSION['id_guru']; // Pastikan id_guru ada di sesi
$tanggal_hari_ini = date('Y-m-d'); // Mendapatkan tanggal hari ini
$hari_ini = date('l'); // Mendapatkan nama hari dalam bahasa Inggris

// Cek apakah hari ini adalah hari Minggu
$is_minggu = ($hari_ini === 'Sunday');

// Query untuk memeriksa absensi hari ini
$query = "SELECT * FROM absensi_guru WHERE id_guru = ? AND tanggal = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("is", $id_guru, $tanggal_hari_ini);
$stmt->execute();
$result = $stmt->get_result();
$absensi_hari_ini = $result->fetch_assoc();

// Status Absensi
$absen_masuk = isset($absensi_hari_ini['waktu_masuk']);
$absen_pulang = isset($absensi_hari_ini['waktu_pulang']);
?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Absensi Guru</h4>
                                        <div class="px-4">
                                            <?php if ($is_minggu): ?>
                                            <!-- Jika hari Minggu, tampilkan teks "Hari Ahad, absensi tidak tersedia" -->
                                            <p class="font-bold mt-3">Hari Ahad, absensi tidak tersedia.</p>
                                            <?php else: ?>
                                            <!-- Jika bukan hari Minggu, tampilkan tombol Absensi Masuk dan Pulang sesuai status absensi -->

                                            <!-- Tombol Absensi Masuk -->
                                            <button class="btn btn-block btn-xl btn-outline-primary font-bold mt-3"
                                                <?php echo $absen_masuk ? 'disabled' : ''; ?> onclick="absen('masuk')">
                                                Absensi Masuk <?php echo date('l, d F Y'); ?>
                                            </button>

                                            <!-- Tombol Absensi Pulang -->
                                            <button class="btn btn-block btn-xl btn-outline-warning font-bold mt-3"
                                                <?php echo (!$absen_masuk || $absen_pulang) ? 'disabled' : ''; ?>
                                                onclick="absen('pulang')">
                                                Absensi Pulang <?php echo date('l, d F Y'); ?>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="diagram absensi guru">
                                            <canvas id="chartAbsensiGuru"></canvas>
                                        </div>
                                    </div>
                                </div>
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
                                        <h5 class="font-bold"> <?php echo htmlspecialchars($guru['nama_guru']); ?>
                                        </h5>
                                        <h6 class="text-muted mb-0"><?php echo htmlspecialchars($guru['jabatan']); ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </section>
            </div>


            <?php
            include 'root/menu-mobile.php';
include 'root/footer.php'
?>




        </div>
    </div>

    <script>
    function absen(type) {
        // Fungsi untuk mengirim absensi masuk atau pulang menggunakan AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "proses_absensi.php", true); // Jalur file PHP untuk memproses absensi
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Refresh halaman setelah absensi berhasil
                location.reload();
            }
        };

        // Kirim data jenis absensi ('masuk' atau 'pulang')
        xhr.send("type=" + type);
    }


    // Data PHP ke JavaScript
    const bulan = <?php echo json_encode($bulan); ?>;
    const jumlahHadir = <?php echo json_encode($jumlah_hadir); ?>;

    // Buat Diagram Batang
    const ctx = document.getElementById('chartAbsensiGuru').getContext('2d');
    const chartAbsensiGuru = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bulan,
            datasets: [{
                label: 'Jumlah Kehadiran',
                data: jumlahHadir,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Kehadiran'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Kehadiran Guru per Bulan'
                }
            }
        }
    });
    </script>

    <?php
include 'root/js.php'
?>
</body>

</html>