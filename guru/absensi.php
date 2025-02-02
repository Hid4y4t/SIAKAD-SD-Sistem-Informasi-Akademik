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

// Proses jika form dikirimkan
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_kelas = $_POST['kode_kelas'];

    // Periksa apakah kode kelas ada di database
    $query = "SELECT * FROM kelas WHERE kode_kelas = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $kode_kelas);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika kode kelas ditemukan, arahkan ke halaman berikutnya
        header("Location: data_kelas.php?kode_kelas=" . urlencode($kode_kelas));
        exit;
    } else {
        // Jika kode kelas tidak ditemukan, tampilkan pesan error
        $error = "Kode kelas tidak dikenal.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<?php
include 'root/head.php';
?>

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
                <h3>Jurnal Kelas</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="col-sm-10">
                            <form method="POST" action="">
                                <div class="input-group mb-3">
                                    <input type="text" name="kode_kelas" class="form-control"
                                        placeholder="Masukan Kode Kelas" aria-label="Kode Kelas"
                                        aria-describedby="inputGroup-sizing-lg" required>
                                    <button class="btn btn-primary" type="submit" id="button-addon1">Masuk</button>
                                </div>
                                <?php if ($error): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php
            include 'root/menu-mobile.php';
include 'root/footer.php'
?>
 <?php include 'root/js.php'; ?>
</body>

</html>