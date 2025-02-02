<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

echo "Selamat datang, Tata Usaha " . htmlspecialchars($_SESSION['nama_admin']);
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
                <h3>Daftar Penerima Potongan SPP</h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- Section untuk Daftar Potongan SPP -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        Daftar Siswa Menerima Potongan SPP
                                        <button type="button" class="btn btn-outline-success block"
                                            data-bs-toggle="modal" data-bs-target="#modalTambahPotongan">
                                            Tambah
                                        </button>
                                    </h5>

                                    <!-- Modal Tambah Penerima Potongan SPP -->
                                    <div class="modal fade" id="modalTambahPotongan" tabindex="-1" role="dialog"
                                        aria-labelledby="modalTambahPotonganTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalTambahPotonganTitle">Tambah
                                                        Penerima Potongan SPP</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                        <i data-feather="x"></i>
                                                    </button>
                                                </div>
                                                <form id="formTambahPotongan" method="POST"
                                                    action="proses_tambah_potongan.php">
                                                    <div class="modal-body">
                                                        <!-- NIS Siswa -->
                                                        <div class="mb-3">
                                                            <label for="nis_siswa" class="form-label">NIS Siswa</label>
                                                            <input type="number" class="form-control" id="nis_siswa"
                                                                name="nis_siswa" required onblur="fetchSiswaData()">
                                                        </div>

                                                        <!-- Nama Siswa (auto-filled) -->
                                                        <div class="mb-3">
                                                            <label for="nama_siswa" class="form-label">Nama Siswa</label>
                                                            <input type="text" class="form-control" id="nama_siswa"
                                                                name="nama_siswa" readonly>
                                                        </div>

                                                        <!-- Jenis Potongan -->
                                                        <div class="mb-3">
                                                            <label for="jenis_potongan" class="form-label">Jenis Potongan</label>
                                                            <select class="form-control" id="jenis_potongan"
                                                                name="jenis_potongan" required
                                                                onchange="fetchJumlahPotongan()">
                                                                <option value="">Pilih Potongan</option>
                                                                <?php
                                                                require '../koneksi/koneksi.php';
                                                                $result = $mysqli->query("SELECT id_potongan, nama_potongan FROM jenis_potongan_spp");
                                                                while ($row = $result->fetch_assoc()) {
                                                                    echo "<option value='" . $row['id_potongan'] . "'>" . $row['nama_potongan'] . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <!-- Jumlah (auto-filled based on potongan selection, readonly) -->
                                                        <div class="mb-3">
                                                            <label for="jumlah" class="form-label">Jumlah</label>
                                                            <input type="number" class="form-control" id="jumlah"
                                                                name="jumlah" readonly>
                                                        </div>

                                                        <!-- Keterangan -->
                                                        <div class="mb-3">
                                                            <label for="keterangan" class="form-label">Keterangan</label>
                                                            <textarea class="form-control" id="keterangan"
                                                                name="keterangan"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary"
                                                            data-bs-dismiss="modal">
                                                            <i class="bx bx-x d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Close</span>
                                                        </button>
                                                        <button type="submit" class="btn btn-primary ms-1">
                                                            <i class="bx bx-check d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Simpan</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Daftar Penerima Potongan SPP -->
                                <div class="card-body"><div class="mb-3">
    <button id="exportAll" class="btn btn-primary">Cetak Semua ke Excel</button>
    <button id="exportSelected" class="btn btn-success">Cetak Pilihan ke Excel</button>
</div>

                                <div class="table-responsive">
    <table class="table" id="tablePotongan">
        <thead>
            <tr>
                <th></th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jenis Potongan</th>
                <th>Nominal</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $query = "SELECT p.id_potongan, s.nama_siswa, s.kelas, jp.nama_potongan, p.jumlah 
                          FROM potongan_spp p
                          JOIN siswa s ON p.id_siswa = s.id_siswa
                          JOIN jenis_potongan_spp jp ON p.jenis_potongan = jp.id_potongan";
                $result = $mysqli->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' class='selectRow' value='" . $row['id_potongan'] . "'></td>";
                    echo "<td>" . htmlspecialchars($row['nama_siswa']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_potongan']) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah'], 2, ',', '.') . "</td>";
                    echo "<td>
                            <form action='hapus_potongan.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id_potongan' value='" . $row['id_potongan'] . "'>
                                <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Anda yakin ingin menghapus data ini?\")'>Hapus</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>

                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section untuk Daftar Jenis Potongan SPP -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        Daftar Jenis Potongan SPP
                                        <button type="button" class="btn btn-outline-success block"
                                            data-bs-toggle="modal" data-bs-target="#modalTambahJenisPotongan">
                                            Tambah Jenis Potongan
                                        </button>
                                    </h5>

                                    <!-- Modal Tambah Jenis Potongan SPP -->
                                    <div class="modal fade" id="modalTambahJenisPotongan" tabindex="-1" role="dialog"
                                        aria-labelledby="modalTambahJenisPotonganTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalTambahJenisPotonganTitle">Tambah
                                                        Jenis Potongan SPP</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                        <i data-feather="x"></i>
                                                    </button>
                                                </div>
                                                <form id="formTambahJenisPotongan" method="POST"
                                                    action="proses_tambah_jenis_potongan.php">
                                                    <div class="modal-body">
                                                        <!-- Nama Potongan -->
                                                        <div class="mb-3">
                                                            <label for="nama_potongan" class="form-label">Nama Potongan</label>
                                                            <input type="text" class="form-control" id="nama_potongan"
                                                                name="nama_potongan" required>
                                                        </div>

                                                        <!-- Keterangan -->
                                                        <div class="mb-3">
                                                            <label for="keterangan_potongan"
                                                                class="form-label">Keterangan</label>
                                                            <textarea class="form-control" id="keterangan_potongan"
                                                                name="keterangan"></textarea>
                                                        </div>

                                                        <!-- Potongan -->
                                                        <div class="mb-3">
                                                            <label for="potongan" class="form-label">Potongan</label>
                                                            <input type="number" class="form-control"
                                                                id="potongan" name="potongan" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary"
                                                            data-bs-dismiss="modal">
                                                            <i class="bx bx-x d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Close</span>
                                                        </button>
                                                        <button type="submit" class="btn btn-primary ms-1">
                                                            <i class="bx bx-check d-block d-sm-none"></i>
                                                            <span class="d-none d-sm-block">Simpan</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Daftar Jenis Potongan SPP -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="tableJenisPotongan">
                                            <thead>
                                                <tr>
                                                    <th>Nama Potongan</th>
                                                    <th>Keterangan</th>
                                                    <th>Potongan</th>
                                                    <th>Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Ambil data jenis potongan dari database
                                                $result = $mysqli->query("SELECT * FROM jenis_potongan_spp");
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['nama_potongan']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                                                    echo "<td>Rp " . number_format($row['potongan'], 2, ',', '.') . "</td>";
                                                    echo "<td>
                                                            <form action='hapus_jenis_potongan.php' method='POST' style='display:inline;'>
                                                                <input type='hidden' name='id_potongan' value='" . $row['id_potongan'] . "'>
                                                                <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Anda yakin ingin menghapus data ini?\")'>Hapus</button>
                                                            </form>
                                                        </td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- JavaScript untuk mengambil data siswa dan jumlah potongan -->


                        <!-- Include jQuery and DataTables CSS/JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTables with pagination, search, and 10 entries per page
    $('#tablePotongan').DataTable({
        "pageLength": 10,
        "lengthChange": false, // Hide the option to change number of records per page
        "language": {
            "search": "Cari:",
            "paginate": {
                "first": "Awal",
                "last": "Akhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            },
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
        }
    });
});

                        function fetchSiswaData() {
                            const nis = document.getElementById('nis_siswa').value;
                            if (nis) {
                                fetch(`get_siswa_data.php?nis=${nis}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data && data.nama_siswa) {
                                            document.getElementById('nama_siswa').value = data.nama_siswa;
                                        } else {
                                            alert('NIS tidak ditemukan.');
                                            document.getElementById('nama_siswa').value = '';
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                            }
                        }

                        function fetchJumlahPotongan() {
                            const idPotongan = document.getElementById('jenis_potongan').value;
                            if (idPotongan) {
                                fetch(`get_jumlah_potongan.php?id_potongan=${idPotongan}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data && data.jumlah) {
                                            document.getElementById('jumlah').value = data.jumlah;
                                        } else {
                                            document.getElementById('jumlah').value = '';
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                            } else {
                                document.getElementById('jumlah').value = '';
                            }
                        }


                        document.getElementById("exportAll").addEventListener("click", function () {
    window.location.href = "export_excel.php?type=all";
});

document.getElementById("exportSelected").addEventListener("click", function () {
    const selected = Array.from(document.querySelectorAll(".selectRow:checked")).map(e => e.value);
    if (selected.length === 0) {
        alert("Silakan pilih data yang ingin dicetak.");
        return;
    }
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "export_excel.php";
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "selected_ids";
    input.value = JSON.stringify(selected);
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
});
                        </script>

                        <?php include 'root/js.php'; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
