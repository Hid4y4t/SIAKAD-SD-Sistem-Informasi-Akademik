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

            <div class="page-heading email-application overflow-hidden">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            
                        </div>
                    </div>
                </div>
                <section class="section content-area-wrapper">
                    <div class="sidebar-left">
                        <div class="sidebar">
                            <div class="sidebar-content email-app-sidebar d-flex">
                                <!-- sidebar close icon -->
                                <span class="sidebar-close-icon">
                                    <i class="bi bi-x"></i>
                                </span>
                                <!-- sidebar close icon -->
                                <div class="email-app-menu">
                                    <div class="form-group form-group-compose">
                                        <!-- compose button  -->
                                        <button type="button" class="btn btn-primary btn-block my-4 compose-btn">
                                            <i class="bi bi-plus"></i>
                                            Tambah Informasi
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <!-- User new mail right area -->
                            <div class="compose-new-mail-sidebar ps">
    <div class="card shadow-none quill-wrapper p-0">
        <div class="card-header">
            <h3 class="card-title" id="emailCompose">Informasi Baru</h3>
            <button type="button" class="close close-icon email-compose-new-close-btn">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <!-- form start -->
        <form action="proses_informasi.php" method="POST" enctype="multipart/form-data" id="compose-form">
            <div class="card-content">
                <div class="card-body pt-0">
                    <div class="form-group pb-50">
                        <label for="recipient">Tujuan</label>
                        <select id="recipient" name="recipient" class="form-control" required>
                            <option value="all">Semua Orang Tua</option>
                            <option value="selected">Pilih Beberapa</option>
                            <option value="single">Satu Orang Tua</option>
                        </select>
                    </div>

                    <div id="select-students" class="form-group pb-50" style="display: none;">
                        <label for="students">Pilih Siswa</label>
                        <select id="students" name="selected_students[]" class="form-control" multiple>
                            <!-- PHP untuk mengambil data siswa dari database -->
                            <?php
                            require_once '../koneksi/koneksi.php';
                            $siswa_query = "SELECT id_siswa, nama_siswa FROM siswa";
                            $siswa_result = $mysqli->query($siswa_query);
                            while ($siswa = $siswa_result->fetch_assoc()) {
                                echo "<option value='{$siswa['id_siswa']}'>{$siswa['nama_siswa']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="judul">Judul</label>
                        <input type="text" id="judul" name="judul" class="form-control" placeholder="Judul Informasi" required>
                    </div>

                    <div class="form-group">
                        <label for="isi">Isi</label>
                        <textarea id="isi" name="isi" class="form-control" placeholder="Isi Informasi" rows="5" required></textarea>
                    </div>

                    <div class="form-group mt-2">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="attachment" id="emailAttach">
                            <label class="custom-file-label" for="emailAttach">Attach File</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end pt-0">
                <button type="reset" class="btn btn-light-secondary cancel-btn me-1">
                    <i class="bi bi-x me-3"></i> <span class="d-sm-inline d-none">Cancel</span>
                </button>
                <button type="submit" class="btn-send btn btn-primary">
                    <i class="bi bi-send me-3"></i> <span class="d-sm-inline d-none">Send</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle input visibility based on recipient selection
    document.getElementById("recipient").addEventListener("change", function() {
        let selectStudents = document.getElementById("select-students");
        if (this.value === "selected") {
            selectStudents.style.display = "block";
        } else if (this.value === "single") {
            selectStudents.style.display = "block";
        } else {
            selectStudents.style.display = "none";
        }
    });
</script>

                            <!--/ User Chat profile right area -->
                        </div>
                    </div>
                    <div class="content-right">
                        <div class="content-overlay"></div>
                        <div class="content-wrapper">
                            <div class="content-header row">
                            </div>
                            <div class="content-body">
                                <!-- email app overlay -->
                           
                                <div class="email-app-area">
                                    <!-- Email list Area -->
                                    <div class="email-app-list-wrapper">
                                        <div class="email-app-list">
                                            <div class="email-action">
                                                <!-- action left start here -->
                                                
                                                <!-- action left end here -->

                                                <!-- action right start here -->
                                                <div
                                                    class="action-right d-flex flex-grow-1 align-items-center justify-content-around">
                                                    <div class="sidebar-toggle d-block d-lg-none">
                                                        <button class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-list fs-5"></i>
                                                        </button>
                                                    </div>
                                                    <!-- search bar  -->
                                                    
                                                    <!-- pagination and page count -->
                                                   
                                                </div>
                                            </div>
                                            <!-- / action right -->

                                            <!-- email user list start -->
                                            <div class="email-user-list list-group ps ps--active-y">
    <ul class="users-list-wrapper media-list">
    <?php
    require_once '../koneksi/koneksi.php';

    // Query untuk mengambil semua informasi dengan nama penerima dari tabel siswa
    $query = "
        SELECT informasi.*, GROUP_CONCAT(siswa.nama_siswa SEPARATOR ', ') AS penerima
        FROM informasi
        LEFT JOIN informasi_penerima ON informasi.id_informasi = informasi_penerima.id_informasi
        LEFT JOIN siswa ON informasi_penerima.id_siswa = siswa.id_siswa
        GROUP BY informasi.id_informasi
        ORDER BY informasi.tanggal_kirim DESC
    ";
    $result = $mysqli->query($query);

    if ($result) {
        $informasiData = $result->fetch_all(MYSQLI_ASSOC); // Ambil data sebagai array asosiatif
    } else {
        echo "Error: " . $mysqli->error;
        $informasiData = []; // Pastikan ada array kosong untuk menghindari error di loop
    }
    ?>

    <?php foreach ($informasiData as $info): ?>
        <li class="media mail-read">
            <div class="user-action">
                <div class="checkbox-con me-3">
                    <div class="checkbox checkbox-shadow checkbox-sm">
                        <input type="checkbox" id="checkbox<?php echo $info['id_informasi']; ?>" class='form-check-input'>
                        <label for="checkbox<?php echo $info['id_informasi']; ?>"></label>
                    </div>
                </div>
            </div>
            <div class="media-body">
                <div class="user-details">
                    <div class="mail-items">
                        <span class="list-group-item-text text-truncate"><?php echo htmlspecialchars($info['judul']); ?></span>
                    </div>
                    <div class="mail-meta-item">
                        <span class="float-right">
                            <span class="mail-date"><?php echo date('d M, Y', strtotime($info['tanggal_kirim'])); ?></span>
                        </span>
                    </div>
                </div>
                <div class="mail-message">
                    <p class="list-group-item-text truncate mb-0">
                        <?php echo htmlspecialchars($info['isi']); ?>
                    </p>
                    <p class="list-group-item-text"><strong>Penerima:</strong> <?php echo htmlspecialchars($info['penerima']); ?></p>
                </div>
                <!-- Tombol untuk menampilkan modal detail -->
                <button type="button" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $info['id_informasi']; ?>">
                    Lihat Detail
                </button>
                <!-- Tombol hapus -->
                <a href="delete_informasi.php?id_informasi=<?php echo $info['id_informasi']; ?>" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Apakah Anda yakin ingin menghapus informasi ini?')">
                    Hapus
                </a>
            </div>
        </li>

        <!-- Modal untuk melihat detail informasi -->
        <div class="modal fade" id="viewModal<?php echo $info['id_informasi']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $info['id_informasi']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewModalLabel<?php echo $info['id_informasi']; ?>">Detail Informasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6>Judul: <?php echo htmlspecialchars($info['judul']); ?></h6>
                        <p>Tanggal Kirim: <?php echo date('d M, Y', strtotime($info['tanggal_kirim'])); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($info['isi'])); ?></p>
                        <p><strong>Penerima:</strong> <?php echo htmlspecialchars($info['penerima']); ?></p>
                        <?php if (!empty($info['file_lampiran'])): ?>
                            <p>Lampiran: <a href="uploads/<?php echo htmlspecialchars($info['file_lampiran']); ?>" target="_blank">Download</a></p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </ul>
</div>

                                        </div>
                                    </div>
                                    <!--/ Email list Area -->

                                    <!-- Detailed Email View -->
                                    <div class="email-app-details">
                                        <!-- email detail view header -->
                                        <div class="email-detail-header">
                                            <div class="email-header-left d-flex align-items-center mb-1">
                                                <span class="go-back me-3">
                                                    <span class="fonticon-wrap d-inline">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </span>
                                                </span>
                                                <h5 class="email-detail-title font-weight-normal mb-0">
                                                    Advertising Internet Online
                                                    <span class="badge bg-light-danger badge-pill ms-1">PRODUCT</span>
                                                </h5>
                                            </div>
                                            <div class="email-header-right mb-1 ms-2 pl-1">
                                                <ul class="list-inline m-0">
                                                    <li class="list-inline-item">
                                                        <button class="btn btn-icon action-icon">
                                                            <span class="fonticon-wrap">
                                                                <i class="fas fa-trash"></i>
                                                            </span>
                                                        </button>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <button class="btn btn-icon action-icon">
                                                            <span class="fonticon-wrap">
                                                                <svg class="bi" width="1.5em" height="1.5em"
                                                                    fill="currentColor">
                                                                    <use
                                                                        xlink:href="assets/static/images/bootstrap-icons.svg#envelope" />
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <div class="dropdown">
                                                            <button class="btn btn-icon dropdown-toggle action-icon"
                                                                id="open-mail-menu" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                <span class="fonticon-wrap">
                                                                    <i class="fas fa-folder"></i>
                                                                </span>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right"
                                                                aria-labelledby="open-mail-menu">
                                                                <a class="dropdown-item" href="#"><i
                                                                        class="bi bi-edit"></i>
                                                                    Draft</a>
                                                                <a class="dropdown-item" href="#"><i
                                                                        class="bi bi-info-circle"></i>
                                                                    Spam</a>
                                                                <a class="dropdown-item" href="#"><i
                                                                        class="bi bi-trash"></i>
                                                                    Trash</a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <div class="dropdown">
                                                            <button class="btn btn-icon dropdown-toggle action-icon"
                                                                id="open-mail-tag" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                <span class="fonticon-wrap">
                                                                    <i class="fas fa-tag"></i>
                                                                </span>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right"
                                                                aria-labelledby="open-mail-tag">
                                                                <a href="#" class="dropdown-item align-items-center">
                                                                    <span
                                                                        class="bullet bullet-success bullet-sm"></span>
                                                                    Product
                                                                </a>
                                                                <a href="#" class="dropdown-item align-items-center">
                                                                    <span
                                                                        class="bullet bullet-primary bullet-sm"></span>
                                                                    Work
                                                                </a>
                                                                <a href="#" class="dropdown-item align-items-center">
                                                                    <span
                                                                        class="bullet bullet-warning bullet-sm"></span>
                                                                    Misc
                                                                </a>
                                                                <a href="#" class="dropdown-item align-items-center">
                                                                    <span class="bullet bullet-danger bullet-sm"></span>
                                                                    Family
                                                                </a>
                                                                <a href="#" class="dropdown-item align-items-center">
                                                                    <span class="bullet bullet-info bullet-sm"></span>
                                                                    Design
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <span class="no-of-list d-none d-sm-block ms-1">1-10 of
                                                            653</span>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <button class="btn btn-icon email-pagination-prev action-icon">
                                                            <i class="bi bi-chevron-left"></i>
                                                        </button>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <button class="btn btn-icon email-pagination-next action-icon">
                                                            <i class="bi bi-chevron-right"></i>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- email detail view header end-->
                                        <div class="email-scroll-area ps ps--active-y">
                                            <!-- email details  -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="collapsible email-detail-head">
                                                        <div class="card collapse-header" role="tablist">
                                                            <div id="headingCollapse5"
                                                                class="card-header d-flex justify-content-between align-items-center"
                                                                data-toggle="collapse" role="tab"
                                                                data-target="#collapse5" aria-expanded="false"
                                                                aria-controls="collapse5">
                                                                <div class="collapse-title media">
                                                                    <div class="pr-1">
                                                                        <div class="avatar me-3">
                                                                            <img src="./assets/compiled/jpg/8.jpg"
                                                                                alt="avtar img holder" width="30"
                                                                                height="30">
                                                                        </div>
                                                                    </div>
                                                                    <div class="media-body mt-25">
                                                                        <span class="text-primary">Elnora Reese</span>
                                                                        <span class="d-sm-inline d-none">
                                                                            &lt;elnora@gmail.com&gt;</span>
                                                                        <small class="text-muted d-block">to Lois
                                                                            Jimenez</small>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="information d-sm-flex d-none align-items-center">
                                                                    <small class="text-muted me-3">15 Jul 2019,
                                                                        10:30</small>
                                                                    <span class="favorite">
                                                                        <i class="bi bi-star me-3"></i>
                                                                    </span>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="dropdown-toggle"
                                                                            id="fisrt-open-submenu"
                                                                            data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            <i
                                                                                class="bi bi-dots-vertical-rounded me-0"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right"
                                                                            aria-labelledby="fisrt-open-submenu">
                                                                            <a href="#"
                                                                                class="dropdown-item mail-reply">
                                                                                <i class="bi bi-share"></i>
                                                                                Reply
                                                                            </a>
                                                                            <a href="#" class="dropdown-item">
                                                                                <i class="bi bi-redo"></i>
                                                                                Forward
                                                                            </a>
                                                                            <a href="#" class="dropdown-item">
                                                                                <i class="bi bi-info-circle"></i>
                                                                                Report Spam
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="collapse5" role="tabpanel"
                                                                aria-labelledby="headingCollapse5" class="collapse">
                                                                <div class="card-content">
                                                                    <div class="card-body py-1">
                                                                        <p class="text-bold-500">Greetings!</p>
                                                                        <p>
                                                                            Lorem Ipsum is simply dummy text of the
                                                                            printing and
                                                                            typesetting industry. Lorem Ipsum has been
                                                                            the industry's standard dummy text ever
                                                                            since the 1500s,
                                                                            when an unknown printer took a galley of
                                                                            type and scrambled it to make a type
                                                                            specimen book.
                                                                        </p>
                                                                        <p>
                                                                            It has survived not only five centuries, but
                                                                            also the
                                                                            leap into electronic typesetting, remaining
                                                                            essentially unchanged.
                                                                        </p>
                                                                        <p class="mb-0">Sincerely yours,</p>
                                                                        <p class="text-bold-500">Envato Design Team</p>
                                                                    </div>
                                                                    <div class="card-footer pt-0 border-top">
                                                                        <label class="sidebar-label">Attached
                                                                            Files</label>
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="cursor-pointer pb-25">
                                                                                <img src="../../../app-assets/static/images/icon/psd.png"
                                                                                    alt="psd.png" height="30">
                                                                                <small
                                                                                    class="text-muted ms-1 attchement-text">uikit-design.psd</small>
                                                                            </li>
                                                                            <li class="cursor-pointer">
                                                                                <img src="../../../app-assets/static/images/icon/sketch.png"
                                                                                    alt="sketch.png" height="30">
                                                                                <small
                                                                                    class="text-muted ms-1 attchement-text">uikit-design.sketch</small>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card collapse-header" role="tablist">
                                                            <div id="headingCollapse6"
                                                                class="card-header d-flex justify-content-between align-items-center"
                                                                data-toggle="collapse" role="tab"
                                                                data-target="#collapse6" aria-expanded="false"
                                                                aria-controls="collapse6">
                                                                <div class="collapse-title media">
                                                                    <div class="pr-1">
                                                                        <div class="avatar me-3">
                                                                            <img src="assets/static/images/faces/19.jpg"
                                                                                alt="avtar img holder" width="30"
                                                                                height="30">
                                                                        </div>
                                                                    </div>
                                                                    <div class="media-body mt-25">
                                                                        <span class="text-primary">Lois Jimenez </span>
                                                                        <span class="d-sm-inline d-none">
                                                                            &lt;lois_jim@gmail.com&gt;</span>
                                                                        <small class="text-muted d-block">to Elnora
                                                                            Reese</small>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="information d-sm-flex d-none align-items-center">
                                                                    <i class="bi bi-paperclip me-3"></i>
                                                                    <small class="text-muted me-3">10 Jul 2019,
                                                                        10:30</small>
                                                                    <span class="favorite">
                                                                        <i class="bi bi-star me-3"></i>
                                                                    </span>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="dropdown-toggle"
                                                                            id="second-open-submenu"
                                                                            data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            <i
                                                                                class="bi bi-dots-vertical-rounded me-0"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right"
                                                                            aria-labelledby="second-open-submenu">
                                                                            <a href="#"
                                                                                class="dropdown-item mail-reply">
                                                                                <i class="bi bi-share"></i>
                                                                                Reply
                                                                            </a>
                                                                            <a href="#" class="dropdown-item">
                                                                                <i class="bi bi-redo"></i>
                                                                                Forward
                                                                            </a>
                                                                            <a href="#" class="dropdown-item">
                                                                                <i class="bi bi-info-circle"></i>
                                                                                Report Spam
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="collapse6" role="tabpanel"
                                                                aria-labelledby="headingCollapse7" class="collapse">
                                                                <div class="card-content">
                                                                    <div class="card-body py-1">
                                                                        <p class="text-bold-500">Greetings!</p>
                                                                        <p>
                                                                            Successful businesses have many things in
                                                                            common, today
                                                                            we’ll look at the big ‘R’of recognition
                                                                            and how a digital advertising network may
                                                                            help.
                                                                            Recognition can be illustrated by two
                                                                            individuals
                                                                            entering a crowded room at a party. Both
                                                                            walk to the far
                                                                            side of the room, one of them slips
                                                                            through the crowd easily and unnoticed as
                                                                            they reach the
                                                                            far side.
                                                                        </p>
                                                                        <p>
                                                                            Gummies sweet tart marzipan lemon drops
                                                                            donut pie.
                                                                            Chocolate cake gingerbread jujubes
                                                                            gingerbread
                                                                            chocolate cake tart bear claw apple pie
                                                                            jelly-o.
                                                                            Gummies biscuit brownie marshmallow oat cake
                                                                            tootsie
                                                                            roll bear claw topping. Oat cake sesame
                                                                            snaps
                                                                            icing cupcake wafer tiramisu jelly-o sugar
                                                                            plum carrot
                                                                            cake
                                                                        </p>
                                                                        <p class="mb-0">Sincerely yours,</p>
                                                                        <p class="text-bold-500">Envato Design Team</p>
                                                                    </div>
                                                                    <div class="card-footer pt-0 border-top">
                                                                        <label class="sidebar-label">Attached
                                                                            Files</label>
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="cursor-pointer pb-25">
                                                                                <img src="../../../app-assets/static/images/icon/psd.png"
                                                                                    alt="psd.png" height="30">
                                                                                <small
                                                                                    class="text-muted ms-1 attchement-text">uikit-design.psd</small>
                                                                            </li>
                                                                            <li class="cursor-pointer">
                                                                                <img src="../../../app-assets/static/images/icon/sketch.png"
                                                                                    alt="sketch.png" height="30">
                                                                                <small
                                                                                    class="text-muted ms-1 attchement-text">uikit-design.sketch</small>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card collapse-header open" role="tablist">
                                                            <div id="headingCollapse7"
                                                                class="card-header d-flex justify-content-between align-items-center"
                                                                data-toggle="collapse" role="tab"
                                                                data-target="#collapse7" aria-expanded="false"
                                                                aria-controls="collapse7">
                                                                <div class="collapse-title media">
                                                                    <div class="pr-1">
                                                                        <div class="avatar me-3">
                                                                            <img src="./assets/compiled/jpg/8.jpg"
                                                                                alt="avtar img holder" width="30"
                                                                                height="30">
                                                                        </div>
                                                                    </div>
                                                                    <div class="media-body mt-25">
                                                                        <span class="text-primary">Elnora Reese</span>
                                                                        <span
                                                                            class="d-sm-inline d-none">&lt;elnora@gmail.com&gt;</span>
                                                                        <small class="text-muted d-block">to Lois
                                                                            Jimenez</small>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="information d-sm-flex d-none align-items-center">
                                                                    <small class="text-muted me-3">05 Jul 2019,
                                                                        10:30</small>
                                                                    <span class="favorite text-warning">
                                                                        <svg class="bi" width="1.5em" height="1.5em"
                                                                            fill="currentColor">
                                                                            <use
                                                                                xlink:href="assets/static/images/bootstrap-icons.svg#star-fill" />
                                                                        </svg>
                                                                    </span>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="dropdown-toggle"
                                                                            id="third-open-menu" data-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false">
                                                                            <i
                                                                                class="bi bi-dots-vertical-rounded me-0"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right"
                                                                            aria-labelledby="second-open-submenu">
                                                                            <a href="#"
                                                                                class="dropdown-item mail-reply">
                                                                                <i class="bi bi-share"></i>
                                                                                Reply
                                                                            </a>
                                                                            <a href="#" class="dropdown-item">
                                                                                <i class="bi bi-redo"></i>
                                                                                Forward
                                                                            </a>
                                                                            <a href="#" class="dropdown-item">
                                                                                <i class="bi bi-info-circle"></i>
                                                                                Report Spam
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="collapse7" role="tabpanel"
                                                                aria-labelledby="headingCollapse7"
                                                                class="collapse show">
                                                                <div class="card-content">
                                                                    <div class="card-body py-1">
                                                                        <p class="text-bold-500">Greetings!</p>
                                                                        <p>
                                                                            It is a long established fact that a reader
                                                                            will be
                                                                            distracted by the readable content of a page
                                                                            when looking at its layout.The point of
                                                                            using Lorem
                                                                            Ipsum is that it has a more-or-less normal
                                                                            distribution of letters, as opposed to using
                                                                            'Content
                                                                            here, content here',making it look like
                                                                            readable English.
                                                                        </p>
                                                                        <p>
                                                                            There are many variations of passages of
                                                                            Lorem Ipsum
                                                                            available, but the majority have suffered
                                                                            alteration in some form, by injected humour,
                                                                            or
                                                                            randomised words which don't look even
                                                                            slightly
                                                                            believable.
                                                                        </p>
                                                                        <p class="mb-0">Sincerely yours,</p>
                                                                        <p class="text-bold-500">Envato Design Team</p>
                                                                    </div>
                                                                    <div class="card-footer pt-0 border-top">
                                                                        <label class="sidebar-label">Attached
                                                                            Files</label>
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="cursor-pointer pb-25">
                                                                                <img src="../../../app-assets/static/images/icon/psd.png"
                                                                                    alt="psd.png" height="30">
                                                                                <small
                                                                                    class="text-muted ms-1 attchement-text">uikit-design.psd</small>
                                                                            </li>
                                                                            <li class="cursor-pointer">
                                                                                <img src="../../../app-assets/static/images/icon/sketch.png"
                                                                                    alt="sketch.png" height="30">
                                                                                <small
                                                                                    class="text-muted ms-1 attchement-text">uikit-design.sketch</small>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- email details  end-->
                                            <div class="row px-2 mb-4">
                                                <!-- quill editor for reply message -->
                                                <div class="col-12 px-0">
                                                    <div class="card shadow-none border rounded">
                                                        <div class="card-body quill-wrapper">
                                                            <span>Reply to Lois Jimenez</span>
                                                            <div class="snow-container" id="detail-view-quill">
                                                                <div class="detail-view-editor ql-container ql-snow">
                                                                    <div class="ql-editor ql-blank" data-gramm="false"
                                                                        data-placeholder="Type something....."
                                                                        contenteditable="true">
                                                                        <p><br></p>
                                                                    </div>
                                                                    <div class="ql-clipboard" tabindex="-1"
                                                                        contenteditable="true">
                                                                    </div>
                                                                    <div class="ql-tooltip ql-hidden"><a
                                                                            class="ql-preview" target="_blank"
                                                                            href="about:blank"></a><input type="text"
                                                                            data-formula="e=mc^2"
                                                                            data-link="https://quilljs.com"
                                                                            data-video="Embed URL"><a
                                                                            class="ql-action"></a><a
                                                                            class="ql-remove"></a></div>
                                                                </div>
                                                                <div class="d-flex justify-content-end">
                                                                    <div
                                                                        class="detail-quill-toolbar ql-toolbar ql-snow">
                                                                        <span class="ql-formats me-3">
                                                                            <button class="ql-bold" type="button"><svg
                                                                                    viewBox="0 0 18 18">
                                                                                    <path class="ql-stroke"
                                                                                        d="M5,4H9.5A2.5,2.5,0,0,1,12,6.5v0A2.5,2.5,0,0,1,9.5,9H5A0,0,0,0,1,5,9V4A0,0,0,0,1,5,4Z">
                                                                                    </path>
                                                                                    <path class="ql-stroke"
                                                                                        d="M5,9h5.5A2.5,2.5,0,0,1,13,11.5v0A2.5,2.5,0,0,1,10.5,14H5a0,0,0,0,1,0,0V9A0,0,0,0,1,5,9Z">
                                                                                    </path>
                                                                                </svg></button>
                                                                            <button class="ql-italic" type="button"><svg
                                                                                    viewBox="0 0 18 18">
                                                                                    <line class="ql-stroke" x1="7"
                                                                                        x2="13" y1="4" y2="4"></line>
                                                                                    <line class="ql-stroke" x1="5"
                                                                                        x2="11" y1="14" y2="14"></line>
                                                                                    <line class="ql-stroke" x1="8"
                                                                                        x2="10" y1="14" y2="4"></line>
                                                                                </svg></button>
                                                                            <button class="ql-underline"
                                                                                type="button"><svg viewBox="0 0 18 18">
                                                                                    <path class="ql-stroke"
                                                                                        d="M5,3V9a4.012,4.012,0,0,0,4,4H9a4.012,4.012,0,0,0,4-4V3">
                                                                                    </path>
                                                                                    <rect class="ql-fill" height="1"
                                                                                        rx="0.5" ry="0.5" width="12"
                                                                                        x="3" y="15"></rect>
                                                                                </svg></button>
                                                                            <button class="ql-link" type="button"><svg
                                                                                    viewBox="0 0 18 18">
                                                                                    <line class="ql-stroke" x1="7"
                                                                                        x2="11" y1="7" y2="11"></line>
                                                                                    <path class="ql-even ql-stroke"
                                                                                        d="M8.9,4.577a3.476,3.476,0,0,1,.36,4.679A3.476,3.476,0,0,1,4.577,8.9C3.185,7.5,2.035,6.4,4.217,4.217S7.5,3.185,8.9,4.577Z">
                                                                                    </path>
                                                                                    <path class="ql-even ql-stroke"
                                                                                        d="M13.423,9.1a3.476,3.476,0,0,0-4.679-.36,3.476,3.476,0,0,0,.36,4.679c1.392,1.392,2.5,2.542,4.679.36S14.815,10.5,13.423,9.1Z">
                                                                                    </path>
                                                                                </svg></button>
                                                                            <button class="ql-image" type="button"><svg
                                                                                    viewBox="0 0 18 18">
                                                                                    <rect class="ql-stroke" height="10"
                                                                                        width="12" x="3" y="4"></rect>
                                                                                    <circle class="ql-fill" cx="6"
                                                                                        cy="7" r="1">
                                                                                    </circle>
                                                                                    <polyline class="ql-even ql-fill"
                                                                                        points="5 12 5 11 7 9 8 10 11 7 13 9 13 12 5 12">
                                                                                    </polyline>
                                                                                </svg></button>
                                                                        </span>
                                                                    </div>
                                                                    <button class="btn btn-primary send-btn">
                                                                        <i class="bi bi-send me-3"></i>
                                                                        <span class="d-none d-sm-inline"> Send</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                                <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;">
                                                </div>
                                            </div>
                                            <div class="ps__rail-y" style="top: 0px; height: 736px; right: 0px;">
                                                <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 626px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/ Detailed Email View -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <script>
            document.querySelector('.sidebar-toggle').addEventListener('click', () => {
                document.querySelector('.email-app-sidebar').classList.toggle('show')
            })
            document.querySelector('.sidebar-close-icon').addEventListener('click', () => {
                document.querySelector('.email-app-sidebar').classList.remove('show')
            })
            document.querySelector('.compose-btn').addEventListener('click', () => {
                document.querySelector('.compose-new-mail-sidebar').classList.add('show')
            })
            document.querySelector('.email-compose-new-close-btn').addEventListener('click', () => {
                document.querySelector('.compose-new-mail-sidebar').classList.remove('show')
            })
            </script>


            <?php
include 'root/js.php'
?>


</body>

</html>