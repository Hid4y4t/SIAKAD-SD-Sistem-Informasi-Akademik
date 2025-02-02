<?php
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nis_siswa = $_POST['nis_siswa'];
    $jenis_beasiswa = $_POST['jenis_beasiswa'];
    $jumlah = $_POST['jumlah'];
    $created_at = $_POST['created_at'];

    // Dapatkan id_siswa berdasarkan nis_siswa
    $stmt = $mysqli->prepare("SELECT id_siswa FROM siswa WHERE nis = ?");
    $stmt->bind_param("s", $nis_siswa);
    $stmt->execute();
    $result = $stmt->get_result();
    $siswa = $result->fetch_assoc();

    if (!$siswa) {
        echo "Error: NIS tidak ditemukan.";
        exit;
    }

    $id_siswa = $siswa['id_siswa'];

    // Simpan data beasiswa ke tabel beasiswa
    $stmt = $mysqli->prepare("INSERT INTO beasiswa (id_siswa, jenis_beasiswa, jumlah, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $id_siswa, $jenis_beasiswa, $jumlah, $created_at);

    if ($stmt->execute()) {
        // Jika berhasil, kembali ke halaman sebelumnya atau tampilkan pesan sukses
        header("Location: index.php?success=1");
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
}
?>
