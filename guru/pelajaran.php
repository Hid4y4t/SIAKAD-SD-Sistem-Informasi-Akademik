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

           
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">

                    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                   Data Absensi Pelajaran
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                        <th>Tanggal</th>
                            <th>Mapel</th>
                            
                            <th>Kelas</th>

                            <th>Materi</th>
                            <th>Catatan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>23 Juni 2024</td>
                            <td>Graiden</td>
                            <td>6</td>
                          
                            <td>
                              Reproduksi
                            </td>
                            <td>pemebelajarn di lab</td>
                            <td><button class="btn btn-primary" type="button" id="button-addon1">Detail</button></td>
                        </tr>
                        <tr>
                        <td>24 Juni 2024</td>
                            <td>Bayu</td>
                            <td>6</td>
                          
                            <td>
                           habitat
                            </td>
                            <th>pembelajaran di lakukan di ruang kelas</th>
                            <td><button class="btn btn-primary" type="button" id="button-addon1">Detail</button></td>
                        </tr>
                        <tr>
                        <td>26 Juni 2024</td>
                            <td>Rina</td>
                            <td>6</td>
                          
                            <td>
                           seni rupa
                            </td>
                            <td>kelas berjalan dengan kondusip</td>
                            <td><button class="btn btn-primary" type="button" id="button-addon1">Detail</button></td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>

    </section>

                </section>
            </div>







        </div>
    </div>

    <?php
include 'root/js.php'
?>


</body>

</html>