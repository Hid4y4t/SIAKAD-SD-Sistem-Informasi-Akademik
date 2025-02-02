<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);

// Query untuk mendapatkan riwayat pembayaran dari `transportasi_history`, menampilkan 20 data terakhir
$queryHistory = "
    SELECT th.*, s.nama_siswa, s.kelas, s.nis, z.nama_zona 
    FROM transportasi_history th
    JOIN transportasi_pembayaran tp ON th.id_pembayaran = tp.id_pembayaran
    JOIN siswa s ON tp.id_siswa = s.id_siswa
    JOIN zona_transportasi z ON tp.id_zona = z.id_zona
    ORDER BY th.tanggal_bayar DESC
    LIMIT 20
";
$resultHistory = $mysqli->query($queryHistory);

if (!$resultHistory) {
    die("Query Error: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

<head>
    <style>
        .action-button {
            display: inline-block;
            width: 100%;
            max-width: 200px;
            padding: 15px 20px;
            margin: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border: none;
            border-radius: 12px;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .action-button:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 12px rgba(0, 0, 0, 0.2);
        }

        .action-button i {
            margin-right: 8px;
        }

        .table-header {
            background-color: #6a11cb;
            color: #fff;
        }
    </style>
</head>

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

            <div class="page-heading text-center">
                <h3>Pembayaran Transportasi</h3>
            </div>
            <div class="page-content">
                <section class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <!-- Payment and Settings Buttons -->
                        <section class="section">
                            <div class="card">
                                <div class="card-body d-flex flex-wrap justify-content-center">
                                    <button class="action-button" data-bs-toggle="modal" data-bs-target="#bayarModal">
                                        <i class="bi bi-credit-card"></i> Bayar
                                    </button>
                                    <button class="action-button" onclick="window.location.href='setting_transportasi.php'">
                                        <i class="bi bi-gear"></i> Pengaturan
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- Table for Transportasi Payment History -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header table-header text-center">
                                    <h4 class="card-title">Riwayat Pembayaran Transportasi</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Nama Siswa</th>
                                                    <th>NIS</th>
                                                    <th>Kelas</th>
                                                    <th>Zona</th>
                                                    <th>Tanggal Bayar</th>
                                                    <th>Total Trip</th>
                                                    <th>Jumlah Bayar</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $resultHistory->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nama_siswa'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nis'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_zona'] ?? ''); ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($row['tanggal_bayar'])); ?></td>
                                                    <td><?php echo htmlspecialchars($row['total_trip'] ?? ''); ?></td>
                                                    <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['metode_pembayaran'] ?? ''); ?></td>
                                                    <td>
                                                        <a href="cetak_nota_transportasi.php?id_history=<?php echo $row['id_history']; ?>" target="_blank" class="btn btn-info btn-sm">Cetak Nota</a>
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

    <?php include 'root/js.php'; ?>

    <!-- Modal for Payment Input -->
    <div class="modal fade" id="bayarModal" tabindex="-1" aria-labelledby="bayarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="proses_nis_transportasi.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bayarModalLabel">Masukkan NIS Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="nis" class="form-control mb-3" placeholder="Masukkan NIS Siswa" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
