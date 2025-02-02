<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="shortcut icon" href="./assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./assets/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./assets/compiled/css/auth.css">
    <style>
        /* Styling khusus halaman registrasi */
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('./assets/logo/2023-06-11.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        #auth-left {
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            margin: auto;
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

        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .form-group {
            flex: 1 1 48%; /* Menjadikan dua kolom */
        }

        .form-group.full-width {
            flex: 1 1 100%; /* Untuk input yang butuh satu kolom penuh */
        }

        .form-control-xl {
            font-size: 15px;
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            width: 100%;
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

        /* Responsive layout */
        @media (max-width: 576px) {
            .form-group {
                flex: 1 1 100%;
            }
            #auth-left {
                width: 90%;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div id="auth-left">
        <div class="auth-logo">
            <a href="index.html"><img src="./assets/logo/snapedit_1727986693962.png" alt="Logo Sekolah"></a>
        </div>
        <h1 class="auth-title">Sign Up</h1>

        <form action="proses_registrasi_admin.php" method="POST">
            <div class="form-container">
                <div class="form-group">
                    <input type="text" class="form-control form-control-xl" name="nama_admin" placeholder="Nama" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control form-control-xl" name="email_admin" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control form-control-xl" name="username_admin" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control form-control-xl" name="telepon_admin" placeholder="Telepon" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control form-control-xl" name="password_admin" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control form-control-xl" name="confirm_password_admin" placeholder="Confirm Password" required>
                </div>
                <div class="form-group full-width">
                    <select class="form-control form-control-xl" name="jabatan" required>
                        <option value="">Pilih Jabatan</option>
                        <option value="TU">Tata Usaha (TU)</option>
                        <option value="Kepsek">Kepala Sekolah (Kepsek)</option>
                        <option value="BK">Bimbingan Konseling (BK)</option>
                        <option value="Kesiswaan">Kesiswaan</option>
                       
                        <option value="Wakur">Wakur</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block shadow-lg mt-4">Sign Up</button>
        </form>

        <div class="text-center mt-4">
            <p class="text-gray-600">Sudah punya akun? <a href="index.php" class="font-bold text-primary">Log in</a>.</p>
        </div>
    </div>
</body>

</html>
