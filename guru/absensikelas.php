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
                    Absensi Hari Ini <a href="absensikelas.php"><button class="btn btn-primary" type="button" id="button-addon1"> Edit Absensi Hari Ini</button></a>
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>nama</th>
                            
                            <th>Kelas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Graiden</td>
                            <td>6</td>
                          
                            <td>
                                <span class="badge bg-success">Masuk</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Bayu</td>
                            <td>6</td>
                          
                            <td>
                                <span class="badge bg-Danger">Alpa</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Rina</td>
                            <td>6</td>
                          
                            <td>
                                <span class="badge bg-Warning">Ijin</span>
                            </td>
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