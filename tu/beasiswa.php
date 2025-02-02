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
                <h3>Daftar Penerima Beasiswa</h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- Section untuk Daftar Beasiswa -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        Daftar Siswa Menerima Beasiswa
                                        <button type="button" class="btn btn-outline-success block"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalCenter1">
                                            Tambah
                                        </button>
                                    </h5>

                                    <!-- Modal Tambah Penerima Beasiswa -->
                                    <div class="modal fade" id="exampleModalCenter1" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalCenterTitle">Tambah Penerima
                                                        Beasiswa</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                        <i data-feather="x"></i>
                                                    </button>
                                                </div>
                                                <form id="formTambahBeasiswa" method="POST"
                                                    action="proses_tambah_beasiswa.php">
                                                    <div class="modal-body">
                                                        <!-- NIS Siswa -->
                                                        <div class="mb-3">
                                                            <label for="nis_siswa" class="form-label">NIS Siswa</label>
                                                            <input type="number" class="form-control" id="nis_siswa"
                                                                name="nis_siswa" required onblur="fetchSiswaData()">
                                                        </div>

                                                        <!-- Nama Siswa (auto-filled) -->
                                                        <div class="mb-3">
                                                            <label for="nama_siswa" class="form-label">Nama
                                                                Siswa</label>
                                                            <input type="text" class="form-control" id="nama_siswa"
                                                                name="nama_siswa" readonly>
                                                        </div>

                                                        <!-- Jenis Beasiswa -->
                                                        <div class="mb-3">
                                                            <label for="jenis_beasiswa" class="form-label">Jenis
                                                                Beasiswa</label>
                                                            <select class="form-control" id="jenis_beasiswa"
                                                                name="jenis_beasiswa" required
                                                                onchange="fetchJumlahBeasiswa()">
                                                                <option value="">Pilih Beasiswa</option>
                                                                <?php
                                                                require '../koneksi/koneksi.php';
                                                                $result = $mysqli->query("SELECT id_beasiswa_js, nama_beasiswa FROM jenis_beasiswa");
                                                                while ($row = $result->fetch_assoc()) {
                                                                    echo "<option value='" . $row['id_beasiswa_js'] . "'>" . $row['nama_beasiswa'] . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <!-- Jumlah (auto-filled based on beasiswa selection, readonly) -->
                                                        <div class="mb-3">
                                                            <label for="jumlah" class="form-label">Jumlah</label>
                                                            <input type="number" class="form-control" id="jumlah"
                                                                name="jumlah" readonly>
                                                        </div>

                                                        <!-- Tanggal Penerimaan -->
                                                        <div class="mb-3">
                                                            <label for="created_at" class="form-label">Tanggal
                                                                Penerimaan</label>
                                                            <input type="date" class="form-control" id="created_at"
                                                                name="created_at" required>
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

                                <!-- Tabel Daftar Penerima Beasiswa -->
                                <div class="card-body"><div class="mb-3">
    <button id="exportAllBeasiswa" class="btn btn-primary">Cetak Semua ke Excel</button>
    <button id="exportSelectedBeasiswa" class="btn btn-success">Cetak Pilihan ke Excel</button>
</div>

                                <div class="table-responsive">
    <table class="table" id="table1">
        <thead>
            <tr>
                <th></th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jenis Beasiswa</th>
                <th>Nominal</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $query = "SELECT b.id_beasiswa, s.nama_siswa, s.kelas, jb.nama_beasiswa, b.jumlah 
                          FROM beasiswa b
                          JOIN siswa s ON b.id_siswa = s.id_siswa
                          JOIN jenis_beasiswa jb ON b.jenis_beasiswa = jb.id_beasiswa_js";
                $result = $mysqli->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' class='selectRowBeasiswa' value='" . $row['id_beasiswa'] . "'></td>";
                    echo "<td>" . htmlspecialchars($row['nama_siswa']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_beasiswa']) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah'], 2, ',', '.') . "</td>";
                    echo "<td>
                            <form action='hapus_beasiswa.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id_beasiswa' value='" . $row['id_beasiswa'] . "'>
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

                       <!-- Section untuk Daftar Jenis Beasiswa -->
<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                Daftar Jenis Beasiswa
                <button type="button" class="btn btn-outline-success block" data-bs-toggle="modal" data-bs-target="#modalTambahJenisBeasiswa">
                    Tambah
                </button>
            </h5>

            <!-- Modal Tambah Jenis Beasiswa -->
            <div class="modal fade" id="modalTambahJenisBeasiswa" tabindex="-1" role="dialog" aria-labelledby="modalTambahJenisBeasiswaTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambahJenisBeasiswaTitle">Tambah Jenis Beasiswa</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                        <form id="formTambahJenisBeasiswa" method="POST" action="proses_tambah_jenis_beasiswa.php">
                            <div class="modal-body">
                                <!-- Nama Beasiswa -->
                                <div class="mb-3">
                                    <label for="nama_beasiswa" class="form-label">Nama Beasiswa</label>
                                    <input type="text" class="form-control" id="nama_beasiswa" name="nama_beasiswa" required>
                                </div>

                                <!-- Keterangan -->
                                <div class="mb-3">
                                    <label for="keterangan_beasiswa" class="form-label">Keterangan</label>
                                    <textarea class="form-control" id="keterangan_beasiswa" name="keterangan"></textarea>
                                </div>

                                <!-- Potongan -->
                                <div class="mb-3">
                                    <label for="potongan_beasiswa" class="form-label">Potongan</label>
                                    <input type="number" class="form-control" id="potongan_beasiswa" name="potongan" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
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

        <!-- Tabel Daftar Jenis Beasiswa -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="tableJenisBeasiswa">
                    <thead>
                        <tr>
                            <th>Nama Beasiswa</th>
                            <th>Keterangan</th>
                            <th>Potongan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil data jenis beasiswa dari database
                        $result = $mysqli->query("SELECT id_beasiswa_js, nama_beasiswa, keterangan, potongan FROM jenis_beasiswa");

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['nama_beasiswa']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                            echo "<td>Rp " . number_format($row['potongan'], 2, ',', '.') . "</td>";
                            echo "<td>
                                    <form action='hapus_jenis_beasiswa.php' method='POST' style='display:inline;'>
                                        <input type='hidden' name='id_beasiswa_js' value='" . $row['id_beasiswa_js'] . "'>
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


                        <!-- JavaScript untuk mengambil data siswa dan jumlah beasiswa -->
                        <script>
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

                        function fetchJumlahBeasiswa() {
                            const idBeasiswa = document.getElementById('jenis_beasiswa').value;
                            if (idBeasiswa) {
                                fetch(`get_jumlah_beasiswa.php?id_beasiswa_js=${idBeasiswa}`)
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


                        document.getElementById("exportAllBeasiswa").addEventListener("click", function () {
    window.location.href = "export_excel_beasiswa.php?type=all";
});

document.getElementById("exportSelectedBeasiswa").addEventListener("click", function () {
    const selected = Array.from(document.querySelectorAll(".selectRowBeasiswa:checked")).map(e => e.value);
    if (selected.length === 0) {
        alert("Silakan pilih data yang ingin dicetak.");
        return;
    }
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "export_excel_beasiswa.php";
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

</body>

</html>