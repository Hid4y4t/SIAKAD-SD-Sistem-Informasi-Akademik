<?php
require 'koneksi/koneksi.php';
session_start();

define('MAX_LOGIN_ATTEMPTS', 5);         // Batas maksimum percobaan login
define('LOGIN_BLOCK_DURATION', 15 * 60); // Blokir selama 15 menit jika batas tercapai

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Akses tidak sah!');
    }
    unset($_SESSION['csrf_token']); // Hapus token setelah digunakan

    $username = trim($_POST['username']); // Pastikan username tidak mengandung spasi
    $password = $_POST['password'];

    // Query untuk mendapatkan data guru berdasarkan username
    $query = "SELECT id_guru, username, password, login_attempts, last_login_attempt, jabatan FROM guru WHERE username = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $guru = $result->fetch_assoc();

        // Hitung waktu sejak percobaan login terakhir
        $time_since_last_attempt = time() - strtotime($guru['last_login_attempt']);

        // Reset login_attempts jika blokir telah berakhir
        if ($time_since_last_attempt > LOGIN_BLOCK_DURATION) {
            $stmt_reset_attempts = $mysqli->prepare("UPDATE guru SET login_attempts = 0 WHERE id_guru = ?");
            $stmt_reset_attempts->bind_param("i", $guru['id_guru']);
            $stmt_reset_attempts->execute();
            $guru['login_attempts'] = 0;
        }

        // Jika percobaan login melebihi batas, tolak akses
        if ($guru['login_attempts'] >= MAX_LOGIN_ATTEMPTS && $time_since_last_attempt <= LOGIN_BLOCK_DURATION) {
            header("Location: login_guru.php?error=blocked");
            exit();
        }

        // Verifikasi password
        if (password_verify($password, $guru['password'])) {
            // Set session untuk login
            $_SESSION['id_guru'] = $guru['id_guru'];
            $_SESSION['loggedin'] = true;
            $_SESSION['jabatan'] = $guru['jabatan'];

            // Reset login_attempts jika login berhasil
            $stmt_reset_attempts = $mysqli->prepare("UPDATE guru SET login_attempts = 0 WHERE id_guru = ?");
            $stmt_reset_attempts->bind_param("i", $guru['id_guru']);
            $stmt_reset_attempts->execute();

            // Redirect berdasarkan jabatan
            switch ($guru['jabatan']) {
                case 'Guru':
                    header("Location: guru/index.php");
                    break;
                case 'Wali Kelas':
                    header("Location: walikelas/index.php");
                    break;
                case 'Kepsek':
                    header("Location: kepsek/index.php");
                    break;
                case 'TU':
                    header("Location: tu/index.php");
                    break;
                case 'Kesiswaan':
                    header("Location: kesiswaan/index.php");
                    break;
                default:
                    header("Location: login_guru.php?error=unknown_jabatan");
            }
            exit();
        } else {
            // Tambah jumlah percobaan login gagal
            $stmt_increase_attempts = $mysqli->prepare("UPDATE guru SET login_attempts = login_attempts + 1, last_login_attempt = NOW() WHERE id_guru = ?");
            $stmt_increase_attempts->bind_param("i", $guru['id_guru']);
            $stmt_increase_attempts->execute();

            header("Location: login_guru.php?error=invalid");
            exit();
        }
    } else {
        // Username tidak ditemukan
        header("Location: login_guru.php?error=invalid");
        exit();
    }
}
?>
