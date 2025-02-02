<?php
require_once '../koneksi/koneksi.php';

session_start();

// Ambil data guru
$query_guru = "SELECT * FROM guru";
$result_guru = $mysqli->query($query_guru);

// Ambil data admin
$query_admin = "SELECT * FROM admin";
$result_admin = $mysqli->query($query_admin);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "root/head.php"; ?>
</head>

<body>
    <div id="app">
        <?php include "root/menu.php"; ?>
        <div id="main">
            <div class="page-heading">
                <h3>Data Guru dan Karyawan</h3>
            </div>
            <div class="page-content">
                <div class="row">
                    <!-- Tabel Guru -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Guru</h4>
                                <button class="btn btn-primary btn-sm" onclick="openTambahGuruModal()">Tambah Guru</button>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Guru</th>
                                            <th>Email</th>
                                            <th>Jabatan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php while ($guru = $result_guru->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($guru['nama_guru']); ?></td>
                                            <td><?= htmlspecialchars($guru['email_guru']); ?></td>
                                            <td><?= htmlspecialchars($guru['jabatan']); ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" onclick="openEditGuruModal(<?= $guru['id_guru']; ?>)">Edit</button>
                                                <a href="hapus_guru.php?id_guru=<?= $guru['id_guru']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus guru ini?')">Hapus</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Admin -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Tandik</h4>
                                <button class="btn btn-primary btn-sm" onclick="openTambahAdminModal()">Tambah Tandik</button>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Tandik</th>
                                            <th>Email</th>
                                            <th>Jabatan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php while ($admin = $result_admin->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($admin['nama_admin']); ?></td>
                                            <td><?= htmlspecialchars($admin['email_admin']); ?></td>
                                            <td><?= htmlspecialchars($admin['jabatan']); ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" onclick="openEditAdminModal(<?= $admin['id_admin']; ?>)">Edit</button>
                                                <a href="hapus_admin.php?id_admin=<?= $admin['id_admin']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus admin ini?')">Hapus</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
   <!-- Modal Tambah Guru -->
<div class="modal fade" id="modalTambahGuru" tabindex="-1" aria-labelledby="modalTambahGuruLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahGuruLabel">Tambah Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="tambah_guru.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_guru" class="form-label">Nama Guru</label>
                        <input type="text" class="form-control" id="nama_guru" name="nama_guru" required>
                    </div>
                    <div class="mb-3">
                        <label for="email_guru" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email_guru" name="email_guru" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="telepon_guru" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="telepon_guru" name="telepon_guru">
                    </div>
                    <div class="mb-3">
                        <label for="alamat_guru" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat_guru" name="alamat_guru"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jabatan_guru" class="form-label">Jabatan</label>
                        <select class="form-select" id="jabatan_guru" name="jabatan" required>
                            <option value="Guru">Guru</option>
                   
                            <option value="Wali Kelas">Wali Kelas</option>
                         
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_mulai_kerja" class="form-label">Tanggal Mulai Kerja</label>
                        <input type="date" class="form-control" id="tanggal_mulai_kerja" name="tanggal_mulai_kerja" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Tambah Admin -->
<div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-labelledby="modalTambahAdminLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahAdminLabel">Tambah Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="tambah_admin.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_admin" class="form-label">Nama Admin</label>
                        <input type="text" class="form-control" id="nama_admin" name="nama_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="username_admin" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username_admin" name="username_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="email_admin" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email_admin" name="email_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="telepon_admin" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="telepon_admin" name="telepon_admin">
                    </div>
                    <div class="mb-3">
                        <label for="jabatan_admin" class="form-label">Jabatan</label>
                        <select class="form-select" id="jabatan_admin" name="jabatan" required>
                            <option value="TU">TU</option>
                            <option value="Kepsek">Kepsek</option>
                            <option value="Wakur">Wakur</option>
  
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password_admin" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password_admin" name="password_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password_admin" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirm_password_admin" name="confirm_password_admin" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditGuru" tabindex="-1" aria-labelledby="modalEditGuruLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="update_guru.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditGuruLabel">Edit Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editGuruId" name="id_guru">
                    <div class="mb-3">
                        <label for="editNamaGuru" class="form-label">Nama Guru</label>
                        <input type="text" class="form-control" id="editNamaGuru" name="nama_guru" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmailGuru" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmailGuru" name="email_guru" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTeleponGuru" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="editTeleponGuru" name="telepon_guru">
                    </div>
                    <div class="mb-3">
                        <label for="editAlamatGuru" class="form-label">Alamat</label>
                        <textarea class="form-control" id="editAlamatGuru" name="alamat_guru"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editJabatanGuru" class="form-label">Jabatan</label>
                        <select class="form-select" id="editJabatanGuru" name="jabatan" required>
                            <option value="Guru">Guru</option>
                          
                            <option value="Wali Kelas">Wali Kelas</option>
                 
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editJenisKelaminGuru" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="editJenisKelaminGuru" name="jenis_kelamin" required>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-labelledby="modalEditAdminLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="update_admin.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditAdminLabel">Edit Tandik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editAdminId" name="id_admin">
                    <div class="mb-3">
                        <label for="editNamaAdmin" class="form-label">Nama Tandik</label>
                        <input type="text" class="form-control" id="editNamaAdmin" name="nama_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmailAdmin" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmailAdmin" name="email_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUsernameAdmin" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editUsernameAdmin" name="username_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTeleponAdmin" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="editTeleponAdmin" name="telepon_admin">
                    </div>
                    <div class="mb-3">
                        <label for="editJabatanAdmin" class="form-label">Jabatan</label>
                        <select class="form-select" id="editJabatanAdmin" name="jabatan" required>
                            <option value="TU">TU</option>
                            <option value="Kepsek">Kepsek</option>
                           
                            <option value="Wakur">Kesiswaan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

    <?php
include 'root/footer.php'
?>




        </div>
    </div>

    <?php
include 'root/js.php'
?>
    <!-- Tambahkan modal untuk admin -->

    <script>
       function openTambahGuruModal() {
    const modal = new bootstrap.Modal(document.getElementById('modalTambahGuru'));
    modal.show();
}



function openTambahAdminModal() {
    const modal = new bootstrap.Modal(document.getElementById('modalTambahAdmin'));
    modal.show();
}

function openEditGuruModal(idGuru) {
    // Ambil data guru berdasarkan ID
    fetch(`get_guru.php?id_guru=${idGuru}`)
        .then(response => response.json())
        .then(data => {
            // Masukkan data ke dalam modal
            document.getElementById('editGuruId').value = data.id_guru;
            document.getElementById('editNamaGuru').value = data.nama_guru;
            document.getElementById('editEmailGuru').value = data.email_guru;
            document.getElementById('editTeleponGuru').value = data.telepon_guru;
            document.getElementById('editAlamatGuru').value = data.alamat_guru;
            document.getElementById('editJabatanGuru').value = data.jabatan;
            document.getElementById('editJenisKelaminGuru').value = data.jenis_kelamin;

            const modal = new bootstrap.Modal(document.getElementById('modalEditGuru'));
            modal.show();
        })
        .catch(error => console.error('Error:', error));
}

function openEditAdminModal(idAdmin) {
    // Ambil data admin berdasarkan ID
    fetch(`get_admin.php?id_admin=${idAdmin}`)
        .then(response => response.json())
        .then(data => {
            // Masukkan data ke dalam modal
            document.getElementById('editAdminId').value = data.id_admin;
            document.getElementById('editNamaAdmin').value = data.nama_admin;
            document.getElementById('editEmailAdmin').value = data.email_admin;
            document.getElementById('editUsernameAdmin').value = data.username_admin;
            document.getElementById('editTeleponAdmin').value = data.telepon_admin;
            document.getElementById('editJabatanAdmin').value = data.jabatan;

            const modal = new bootstrap.Modal(document.getElementById('modalEditAdmin'));
            modal.show();
        })
        .catch(error => console.error('Error:', error));
}

    </script>
</body>

</html>
