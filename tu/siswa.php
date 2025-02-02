<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki jabatan 'TU'
if (!isset($_SESSION['loggedin']) || $_SESSION['jabatan'] !== 'TU') {
    header("Location: ../index.php");
    exit;
}

require '../koneksi/koneksi.php';
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
                <h3>Siswa</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <button class="btn icon icon-left btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                            <i data-feather="edit"></i> Tambah Siswa Baru
                                        </button>
                                        <button class="btn icon icon-left btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                                            <i data-feather="edit"></i> Import Siswa Baru
                                        </button>
                                    </h5>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive datatable-minimal">
                                    <input type="text" id="searchInput" placeholder="Cari data..." class="form-control mb-3" onkeyup="searchTable()">
                                        <table class="table" id="tableSiswa">
                                            <thead>
                                                <tr>
                                             
                                                <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"> Pilih</th>
                                                <th>NIS</th>    
                                                <th>Nama</th>
                                                    <th>Kelas</th>
                                                    <th>Ttl</th>
                                                    <th>Status</th>
                                                    <th>Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $query = "SELECT * FROM siswa";
                                                $result = $mysqli->query($query);
                                                while ($row = $result->fetch_assoc()) {
                                                    $status = $row['status'] == 1 ? 'Aktif' : ($row['status'] == 0 ? 'Off' : 'Pindah');
                                                    echo "<tr>
                                                     
                                                     <td><input type='checkbox' class='selectItem' value='{$row['nis']}'></td>
                                                     <td>{$row['nis']}</td>      
                                                     <td>{$row['nama_siswa']}</td>
                                                            <td>{$row['kelas']}</td>
                                                            <td>{$row['tanggal_lahir']}</td>
                                                            <td><span class='badge bg-".($row['status'] == 1 ? "success" : ($row['status'] == 0 ? "secondary" : "warning"))."'>{$status}</span></td>
                                                            <td>
                                                                <button class='btn btn-primary btn-edit' data-id='{$row['id_siswa']}'>Edit</button>
                                                                <button class='btn btn-secondary btn-detail' data-id='{$row['id_siswa']}'>Detail</button>
                                                                <button class='btn btn-danger btn-delete' data-id='{$row['id_siswa']}'>Hapus</button>
                                                            </td>
                                                        </tr>";
                                                }
                                                ?>
                                            </tbody>
                                            <button class="btn btn-outline-primary mt-3" onclick="cetakDataTerpilih()">Export ke PDF</button>
                                            <button class="btn btn-outline-primary mt-3" onclick="exportToExcel()">Export ke Excel</button>
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

    <!-- Modal Tambah Siswa -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="proses_siswa.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Siswa Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="nis" class="form-control mb-3" placeholder="NIS" required>
                    <input type="text" name="nama_siswa" class="form-control mb-3" placeholder="Nama Siswa" required>
                    <input type="email" name="email_siswa" class="form-control mb-3" placeholder="Email Siswa" required>
                    <input type="text" name="telepon_siswa" class="form-control mb-3" placeholder="Telepon">
                    <textarea name="alamat_siswa" class="form-control mb-3" placeholder="Alamat"></textarea>
                    <input type="date" name="tanggal_lahir" class="form-control mb-3" placeholder="Tanggal Lahir">
                    
                    <!-- Pilihan Jenis Kelamin -->
                    <select name="jenis_kelamin" class="form-control mb-3" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    
                    <input type="text" name="kelas" class="form-control mb-3" placeholder="Kelas">
                    <input type="text" name="angkatan" class="form-control mb-3" placeholder="Tahun Angkatan" required>
                     <!-- Pilihan Jenis Kelamin -->
                     <select name="jenis_kelamin" class="form-control mb-3" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    
                    <select name="status" class="form-control mb-3">
                        <option value="1">Aktif</option>
                        <option value="0">Off</option>
                        <option value="2">Pindah</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- Modal Import Siswa -->
   <!-- Modal Import Siswa -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="import_siswa.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file_siswa" class="form-label">Pilih File</label>
                        <input type="file" name="file_siswa" id="file_siswa" class="form-control" accept=".xls, .xlsx" required>
                    </div>
                    <div class="mt-3">
                        <a href="vendor/format_siswa.xlsx" class="btn btn-info" download>Download Format</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- Modal Edit Siswa -->
    <div class="modal fade" id="exampleModalCenter2" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditSiswa" action="update_siswa.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_siswa" name="id_siswa">

                        <input type="text" id="edit_nis" name="nis" class="form-control mb-3" placeholder="nis" required>
                        <input type="text" id="edit_nama_siswa" name="nama_siswa" class="form-control mb-3" placeholder="Nama Siswa" required>
                        <input type="email" id="edit_email_siswa" name="email_siswa" class="form-control mb-3" placeholder="Email Siswa" required>
                        <input type="text" id="edit_telepon_siswa" name="telepon_siswa" class="form-control mb-3" placeholder="Telepon">
                        <textarea id="edit_alamat_siswa" name="alamat_siswa" class="form-control mb-3" placeholder="Alamat"></textarea>
                        <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir" class="form-control mb-3" placeholder="Tanggal Lahir">
                        <input type="text" id="edit_kelas" name="kelas" class="form-control mb-3" placeholder="Kelas">
                        <input type="text" id="edit_angkatan" name="angkatan" class="form-control mb-3" placeholder="Angkatan">
                        <input type="text" id="edit_jenis_kelamin" name="jenis_kelamin" class="form-control mb-3" placeholder="jenis kelamin">
                        <select id="edit_status" name="status" class="form-control mb-3">
                            <option value="1">Aktif</option>
                            <option value="0">Off</option>
                            <option value="2">Pindah</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Siswa -->
    <div class="modal fade" id="exampleModalCenter3" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <input type="text" id="detail_nis" class="form-control mb-3" readonly>
                    <input type="text" id="detail_nama_siswa" class="form-control mb-3" readonly>
                    <input type="email" id="detail_email_siswa" class="form-control mb-3" readonly>
                    <input type="text" id="detail_telepon_siswa" class="form-control mb-3" readonly>
                    <textarea id="detail_alamat_siswa" class="form-control mb-3" readonly></textarea>
                    <input type="text" id="detail_tanggal_lahir" class="form-control mb-3" readonly>
                    <input type="text" id="detail_kelas" class="form-control mb-3" readonly>
                    <input type="text" id="detail_angkatan" class="form-control mb-3" readonly>
                    <input type="text" id="detail_jenis_kelamin" class="form-control mb-3" readonly>
                    <input type="text" id="detail_status" class="form-control mb-3" readonly>
                    <input type="text" id="created_at" class="form-control mb-3" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function () {
        // Menampilkan Data untuk Edit
        $(document).on('click', '.btn-edit', function () {
            const siswaId = $(this).data('id');
            $.get(`get_siswa.php?id=${siswaId}`, function (data) {
                const siswa = JSON.parse(data);
                $('#edit_id_siswa').val(siswa.id_siswa);
                $('#edit_nis').val(siswa.nis);
                $('#edit_nama_siswa').val(siswa.nama_siswa);
                $('#edit_email_siswa').val(siswa.email_siswa);
                $('#edit_telepon_siswa').val(siswa.telepon_siswa);
                $('#edit_alamat_siswa').val(siswa.alamat_siswa);
                $('#edit_tanggal_lahir').val(siswa.tanggal_lahir);
                $('#edit_kelas').val(siswa.kelas);
                $('#edit_jenis_kelamin').val(siswa.jenis_kelamin);
                $('#edit_angkatan').val(siswa.angkatan);
                $('#edit_status').val(siswa.status);
                $('#exampleModalCenter2').modal('show');
            });
        });

        // Menampilkan Data untuk Detail
        $(document).on('click', '.btn-detail', function () {
            const siswaId = $(this).data('id');
            $.get(`get_siswa.php?id=${siswaId}`, function (data) {
                const siswa = JSON.parse(data);
                $('#detail_nis').val(siswa.nis);
                $('#detail_nama_siswa').val(siswa.nama_siswa);
                $('#detail_email_siswa').val(siswa.email_siswa);
                $('#detail_telepon_siswa').val(siswa.telepon_siswa);
                $('#detail_alamat_siswa').val(siswa.alamat_siswa);
                $('#detail_tanggal_lahir').val(siswa.tanggal_lahir);
                $('#detail_kelas').val(siswa.kelas);
                $('#detail_angkatan').val(siswa.angkatan);
                $('#detail_jenis_kelamin').val(siswa.jenis_kelamin);
                $('#detail_status').val(siswa.status == 1 ? 'Aktif' : siswa.status == 0 ? 'Off' : 'Pindah');
                $('#created_at').val(siswa.created_at);
                $('#exampleModalCenter3').modal('show');
            });
        });

        // Menghapus Data Siswa
        $(document).on('click', '.btn-delete', function () {
            const siswaId = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin menghapus data siswa ini?")) {
                $.post('hapus_siswa.php', { id: siswaId }, function () {
                    location.reload();
                });
            }
        });
    });


    function searchTable() {
        // Ambil input pencarian
        const input = document.getElementById("searchInput");
        const filter = input.value.toLowerCase();
        const table = document.getElementById("tableSiswa");
        const tr = table.getElementsByTagName("tr");

        // Loop melalui semua baris tabel dan sembunyikan yang tidak cocok dengan pencarian
        for (let i = 1; i < tr.length; i++) { // Mulai dari i = 1 untuk mengabaikan header
            let tdArray = tr[i].getElementsByTagName("td");
            let found = false;

            // Loop melalui setiap kolom dalam baris
            for (let j = 0; j < tdArray.length; j++) {
                if (tdArray[j]) {
                    const textValue = tdArray[j].textContent || tdArray[j].innerText;
                    if (textValue.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }

            if (found) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }


     // Fungsi untuk memilih semua checkbox
     function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('.selectItem');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }

    // Fungsi untuk mengumpulkan data terpilih dan mencetaknya
    function cetakDataTerpilih() {
        const selected = [];
        const checkboxes = document.querySelectorAll('.selectItem:checked');

        checkboxes.forEach(checkbox => {
            selected.push(checkbox.value); // Ambil nilai NIS yang dipilih
        });

        if (selected.length > 0) {
            // Kirim data yang dipilih ke server untuk dicetak
            window.location.href = `cetak_siswa.php?ids=${selected.join(",")}`;
        } else {
            alert("Silakan pilih data siswa yang ingin dicetak.");
        }
    }

    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('.selectItem');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }

    function exportToExcel() {
        const selected = [];
        const checkboxes = document.querySelectorAll('.selectItem:checked');

        checkboxes.forEach(checkbox => {
            selected.push(checkbox.value);
        });

        if (selected.length > 0) {
            // Kirim data yang dipilih ke PHP untuk ekspor Excel
            window.location.href = `export_siswa_excel.php?ids=${selected.join(",")}`;
        } else {
            alert("Silakan pilih data siswa yang ingin diekspor.");
        }
    }
    </script>
</body>
</html>
