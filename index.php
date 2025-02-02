<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, rgba(0, 123, 255, 0.8), rgba(0, 193, 142, 0.8)),
                url('DALL·E\ 2024-11-20\ 10.43.40\ -\ A\ professional\ and\ visually\ appealing\ background\ illustration\ for\ an\ elementary\ school\ education\ website.\ The\ design\ features\ a\ clean\ and\ modern\ style.webp') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 20px 20px 0 0;
            width: 400px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, #007bff, #00c18e);
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 20px 20px 0 0;
        }

        .btn-custom {
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 10px;
            margin: 10px 0;
            padding: 15px 20px;
            transition: all 0.3s ease-in-out;
        }

        .btn-custom i {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .btn-media {
            background-color: #ff6f61;
            color: white;
        }

        .btn-tenaga {
            background-color: #ffc107;
            color: white;
        }

        .btn-guru {
            background-color: #28a745;
            color: white;
        }

        .btn-custom:hover {
            opacity: 0.85;
            transform: translateY(-3px);
        }

        .footer-text {
            text-align: center;
            font-size: 0.85rem;
            margin-top: 20px;
            color: #ffffff;
        }

        .footer-text a {
            color: #ffc107;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-school"></i> Portal Login
        </div>
        <div class="card-body text-center">
            <p class="mb-4 text-muted">Pilih tipe login Anda untuk melanjutkan</p>

            <!-- Login Media -->
            <a href="https://sdmulabelkalibaru.sch.id/login/" class="btn btn-custom btn-media">
                <i class="fas fa-video"></i> Login Media
            </a>

            <!-- Login Tenaga Pendidik -->
            <a href="index_admin.php" class="btn btn-custom btn-tenaga">
                <i class="fas fa-chalkboard-teacher"></i> Login Tenaga Pendidik
            </a>

            <!-- Login Guru/Wali Kelas -->
            <a href="login_guru.php" class="btn btn-custom btn-guru">
                <i class="fas fa-user-tie"></i> Login Guru/Wali Kelas
            </a>
        </div>
    </div>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
