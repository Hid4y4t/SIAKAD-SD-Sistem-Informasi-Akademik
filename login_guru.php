<?php
session_start();
// Membuat token CSRF jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Guru - Sekolah</title>
    <link rel="shortcut icon" href="./assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./assets/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./assets/compiled/css/auth.css">
    <style>
        /* Styling halaman login */
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('./assets/logo/IMG-20210910-WA0013.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Roboto', sans-serif;
        }

        #auth-left {
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(5px);
        }

        .auth-logo img {
            max-width: 80px;
            margin-bottom: 1.5rem;
        }

        .auth-title {
            font-size: 26px;
            font-weight: 700;
            color: #333333;
            margin-bottom: 1rem;
        }

        .auth-subtitle {
            font-size: 15px;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .form-control-xl {
            font-size: 15px;
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            margin-bottom: 1rem;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            font-size: 16px;
            padding: 0.75rem;
            border-radius: 5px;
            width: 100%;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .text-gray-600 {
            color: #6c757d;
        }

        .form-check-label {
            font-size: 14px;
        }

        /* Styling responsif */
        @media (max-width: 576px) {
            #auth-left {
                padding: 1.5rem;
                margin: 1rem;
            }

            .auth-title {
                font-size: 22px;
            }

            .auth-subtitle {
                font-size: 14px;
            }

            .btn-primary {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div id="auth-left">
        <div class="auth-logo">
            <a href="index.php"><img src="./assets/logo/snapedit_1727986693962.png" alt="Logo Sekolah"></a>
        </div>
        <h1 class="auth-title">Login Guru</h1>
        <p class="auth-subtitle">Masukkan data Anda untuk mengakses sistem absensi guru.</p>
      
        <!-- Form login untuk guru -->
        <form action="proses_login_guru.php" method="POST">
     
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <div class="form-group position-relative has-icon-left">
                <input type="text" class="form-control form-control-xl" name="username" placeholder="Username" required>
                <div class="form-control-icon">
                    <i class="bi bi-person"></i>
                </div>
            </div>
            <div class="form-group position-relative has-icon-left">
                <input type="password" class="form-control form-control-xl" name="password" placeholder="Password" required>
                <div class="form-control-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
            </div>
            <div class="form-check form-check-lg d-flex align-items-center mb-4">
                <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                <label class="form-check-label text-gray-600" for="flexCheckDefault">
                    Tetap masuk
                </label>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Masuk</button>
        </form>
 <!-- Pesan Kesalahan -->
 <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger mt-3">
                            <?php
                            switch ($_GET['error']) {
                                case 'invalid':
                                    echo "Username atau password salah.";
                                    break;
                                case 'blocked':
                                    echo "Akun Anda diblokir sementara karena terlalu banyak percobaan login.";
                                    break;
                                default:
                                    echo "Terjadi kesalahan, silakan coba lagi.";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
        <!-- Tampilkan pesan error jika ada -->
        <?php if (isset($_GET['error'])): ?>
            <p class="text-danger mt-2">Username atau password salah!</p>
        <?php endif; ?>

        <div class="text-center mt-4">
            <!--<p class="text-gray-600">Belum punya akun? <a href="registrasi_guru.php" class="font-bold text-primary">Daftar</a>.</p>-->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
