<?php
require_once '../koneksi/koneksi.php';

session_start();

// Periksa apakah pengguna sudah login dan apakah jabatan mereka sesuai
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'Guru') {
    header("Location: ../login_guru.php");
    exit;
}

// Ambil informasi guru dari session
$id_guru = $_SESSION['id_guru'] ?? null;
if (!$id_guru) {
    die("ID guru tidak ditemukan. Silakan login kembali.");
}

// Ambil data absensi bulanan untuk guru ini
$query_bulan = "
    SELECT DISTINCT DATE_FORMAT(tanggal, '%Y-%m') AS bulan 
    FROM absensi_guru 
    WHERE id_guru = ? 
    ORDER BY bulan DESC
";
$stmt_bulan = $mysqli->prepare($query_bulan);
if (!$stmt_bulan) {
    die("Query gagal dipersiapkan: " . $mysqli->error);
}

$stmt_bulan->bind_param("i", $id_guru);
if (!$stmt_bulan->execute()) {
    die("Eksekusi query gagal: " . $stmt_bulan->error);
}

$result_bulan = $stmt_bulan->get_result();
if (!$result_bulan) {
    die("Gagal mengambil hasil: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'root/head.php'; ?>
<!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">-->
<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include 'root/menu.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Data Absensi Guru</h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Data Absensi Bulanan</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped" id="table1">
                                        <thead>
                                            <tr>
                                                <th>Bulan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
    <?php if ($result_bulan->num_rows > 0): ?>
        <?php while ($bulan = $result_bulan->fetch_assoc()): ?>
        <tr>
            <td><?= date("F Y", strtotime($bulan['bulan'])); ?></td>
           <td>
    <a 
        href="detail_absensi.php?bulan=<?= $bulan['bulan']; ?>&id_guru=<?= $id_guru; ?>" 
        class="btn btn-primary btn-sm">
        Lihat
    </a>
</td>

        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="2">Tidak ada data absensi untuk ditampilkan.</td>
        </tr>
    <?php endif; ?>
</tbody>

                                        
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>
            </div>
        </div>
    </div>

     <!-- Modal Template -->
     <!-- Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">Detail Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody id="modalTableBody">
                        <tr>
                            <td colspan="4">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>-->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const btnLihat = document.querySelectorAll('.btn-lihat');
    const modalTableBody = document.getElementById('modalTableBody');
    const modalElement = document.getElementById('modalDetail');
    const modal = new bootstrap.Modal(modalElement); // Pastikan ID modal sesuai

    btnLihat.forEach(button => {
        button.addEventListener('click', async () => {
            const bulan = button.getAttribute('data-bulan');
            const idGuru = <?= $id_guru; ?>;

            // Kosongkan isi modal terlebih dahulu
            modalTableBody.innerHTML = '<tr><td colspan="4">Loading...</td></tr>';

            try {
                // Fetch data dari server
                const response = await fetch(`get_absensi.php?id_guru=${idGuru}&bulan=${bulan}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();

                // Isi tabel modal berdasarkan data respons
                if (data.length > 0) {
                    modalTableBody.innerHTML = data.map((item, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.waktu_masuk}</td>
                            <td>${item.waktu_pulang}</td>
                            <td>${item.tanggal}</td>
                        </tr>
                    `).join('');
                } else {
                    modalTableBody.innerHTML = '<tr><td colspan="4">Tidak ada data absensi untuk bulan ini.</td></tr>';
                }

                // Tampilkan modal
                modal.show();
            } catch (error) {
                console.error('Error fetching data:', error);
                modalTableBody.innerHTML = '<tr><td colspan="4">Terjadi kesalahan saat memuat data.</td></tr>';
            }
        });
    });
});

    </script>
    <?php include 'root/menu-mobile.php'; ?>
    <?php include 'root/footer.php'; ?>
    <?php include 'root/js.php'; ?>
</body>
</html>
