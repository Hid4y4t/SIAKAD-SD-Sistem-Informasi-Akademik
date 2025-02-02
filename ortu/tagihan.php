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
                  Tagihan Belum Lunas
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                        <th>Bulan Tagihan</th>
                            <th>Jenis Tagihan</th>
                            
                            
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>Juni</td>
                            <td>SPP</td>
                           
                          
                            <td>
                              <span class="badge bg-danger">Belum</span>  
                            </td>
                            <td><button class="btn btn-primary" type="button" id="button-addon1">Detail</button></td>
                        </tr>
                        <tr>
                        <td>Juni</td>
                            <td>Infaq</td>
                         
                          
                            <td>
                             <span class="badge bg-danger">Belum</span> 
                            </td>
                            <td><button class="btn btn-primary" type="button" id="button-addon1">Detail</button></td>
                        </tr>
                        <tr>
                        <td>Juli</td>
                            <td>Infaq</td>
                           
                          
                            <td>
                        <span class="badge bg-danger">Belum</span> 
                            </td>
                            <td><button class="btn btn-primary" type="button" id="button-addon1">Detail</button></td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>


            
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                  Tagihan Sudah Lunas
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                        <th>Bulan Tagihan</th>
                            <th>Jenis Tagihan</th>
                            
                            
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>Mei</td>
                            <td>SPP</td>
                           
                          
                            <td>
                            <span class="badge bg-success">Lunas</span> 
                            </td>
                            <td><button class="btn btn-primary" type="button" id="button-addon1">Detail</button></td>
                        </tr>
                        <tr>
                        <td>Febuari</td>
                            <td>Infaq</td>
                         
                          
                            <td>
                            <span class="badge bg-success">Lunas</span>  
                            </td>
                            <td><button class="btn btn-primary" type="button" id="button-addon1">Detail</button></td>
                        </tr>
                        <tr>
                        <td>Maret</td>
                            <td>Infaq</td>
                           
                          
                            <td>
                            <span class="badge bg-success">Lunas</span> 
                            </td>
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