<?php
// Menggunakan konstanta untuk detail koneksi agar lebih aman
define ('DB_SERVER', 'localhost');
define('DB_USERNAME', 'u220341190_mulabel2'); // Sesuaikan dengan username database Anda
define('DB_PASSWORD', '@Hidayatullah23'); // Sesuaikan dengan password database Anda
define('DB_NAME', 'u220341190_mulabel2');

// Membuat koneksi dengan menggunakan mysqli
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Memeriksa koneksi
if ($mysqli === false) {
    die("ERROR: Tidak dapat terhubung ke database. " . $mysqli->connect_error);
}

// Menggunakan fungsi prepared statement untuk mencegah SQL injection
function executeQuery($query, $params = [], $types = "")
{
    global $mysqli;
    $stmt = $mysqli->prepare($query);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

// Menutup koneksi dengan aman
function closeConnection() {
    global $mysqli;
    $mysqli->close();
}
?>
