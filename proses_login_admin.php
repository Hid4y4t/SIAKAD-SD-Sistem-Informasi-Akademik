<?php
// Pastikan tidak ada spasi atau karakter sebelum tag PHP ini

// Set session cookie parameters sebelum session_start()
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1); // Mencegah akses JavaScript ke cookie

// Nonaktifkan cookie_secure karena masih menggunakan localhost (tidak HTTPS)
ini_set('session.cookie_secure', 0);   // Tidak perlu HTTPS di localhost

ini_set('session.cookie_samesite', 'Strict'); // Mencegah pengiriman cookie dalam permintaan lintas situs

session_start();

require_once 'koneksi/koneksi.php'; // Menggunakan koneksi database

// Fungsi untuk mengunci akun setelah beberapa percobaan gagal
function login_attempt_failed($id_admin) {
    $query = "UPDATE admin SET login_attempts = login_attempts + 1, last_login_attempt = NOW() WHERE id_admin = ?";
    executeQuery($query, [$id_admin], 'i');
}

// Fungsi untuk mereset percobaan login setelah berhasil login
function reset_login_attempts($id_admin) {
    $query = "UPDATE admin SET login_attempts = 0 WHERE id_admin = ?";
    executeQuery($query, [$id_admin], 'i');
}

// Mengecek apakah form sudah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = trim($_POST['username_admin']);
    $password = $_POST['password_admin'];

    // Validasi input
    if (empty($username) || empty($password)) {
        echo "Silakan isi semua field.";
        exit;
    }

    // Query untuk mencari admin berdasarkan username
    $query = "SELECT id_admin, nama_admin, username_admin, password_admin, jabatan, login_attempts, last_login_attempt FROM admin WHERE username_admin = ?";
    $stmt = executeQuery($query, [$username], 's');
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id_admin, $nama_admin, $username_admin, $hashed_password, $jabatan, $login_attempts, $last_login_attempt);
        $stmt->fetch();

        // Cek apakah login attempts sudah melebihi batas
        $max_attempts = 3;
        $lockout_time = 15 * 60; // 15 menit
        $time_since_last_attempt = time() - strtotime($last_login_attempt);

        if ($login_attempts >= $max_attempts && $time_since_last_attempt < $lockout_time) {
            echo "Akun Anda terkunci sementara. Coba lagi dalam 15 menit.";
            exit;
        }
        

        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Reset login attempts jika login berhasil
            reset_login_attempts($id_admin);

            // Amankan session
            session_regenerate_id(true);

            // Simpan data ke session
            $_SESSION['loggedin'] = true;
            $_SESSION['id_admin'] = $id_admin;
            $_SESSION['nama_admin'] = $nama_admin;
            $_SESSION['jabatan'] = $jabatan;

            // Arahkan berdasarkan jabatan
            switch ($jabatan) {
                case 'Kepsek':
                    header("Location: Kepsek/dashboard.php");
                    break;
                case 'TU':
                    header("Location: tu/index.php");
                    break;
                case 'BK':
                    header("Location: BK/dashboard.php");
                    break;
                case 'Kesiswaan':
                    header("Location: Kesiswaan/dashboard.php");
                    break;

                case 'Wakur':
                    header("Location: Wakur/index.php");
                     break;
                default:
                    header("Location: index.php"); // Default jika jabatan tidak dikenal
                    break;
            }
            exit;
        } else {
            // Jika password salah, tambahkan percobaan login
            login_attempt_failed($id_admin);
            echo "Password salah. Silakan coba lagi.";
        }
    } else {
        echo "Username tidak ditemukan. Silakan coba lagi.";
    }
}
?>