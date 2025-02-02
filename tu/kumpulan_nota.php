<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require_once '../koneksi/koneksi.php';

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

<head>
    <style>
        .payment-button {
            display: inline-block;
            width: 100%;
            max-width: 250px;
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
        }

        .payment-button:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 12px rgba(0, 0, 0, 0.2);
        }

        .payment-button i {
            margin-right: 8px;
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
                <h3>Kumpulan Nota Pembayaran</h3>
            </div>
            <div class="page-content">
                <section class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <!-- Tombol Kumpulan Nota Pembayaran -->
                        <section class="section">
                            <div class="">
                                <div class="card-body d-flex flex-wrap justify-content-center">
                                    <button class="payment-button" onclick="window.location.href='nota_spp.php'">
                                        <i class="bi bi-file-earmark-text"></i> Nota Bayar SPP
                                    </button>
                                    <button class="payment-button" onclick="window.location.href='nota_dana_sharing.php'">
                                        <i class="bi bi-cash"></i> Nota Pembayaran Dana Sharing
                                    </button>
                                    <button class="payment-button" onclick="window.location.href='nota_buku.php'">
                                        <i class="bi bi-book"></i> Nota Pembayaran Buku
                                    </button>
                                    <button class="payment-button" onclick="window.location.href='nota_dana_pengembangan.php'">
                                        <i class="bi bi-bar-chart"></i> Nota Pembayaran Dana Pengembangan
                                    </button>
                                    <button class="payment-button" onclick="window.location.href='nota_ppdb_tp.php'">
                                        <i class="bi bi-card-list"></i> Nota Pembayaran PPDB TP
                                    </button>
                                    <button class="payment-button" onclick="window.location.href='nota_transportasi.php'">
                                        <i class="bi bi-truck"></i> Nota Pembayaran Transportasi
                                    </button>
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
