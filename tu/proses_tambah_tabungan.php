<?php
require_once '../koneksi/koneksi.php';

// Cek apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil NIS dari form
    $nis = $_POST['nis'];
    $pemasukan = isset($_POST['pemasukan']) ? (float) $_POST['pemasukan'] : 0;
    $pengeluaran = isset($_POST['pengeluaran']) ? (float) $_POST['pengeluaran'] : 0;
    $keterangan = $_POST['keterangan'] ?? '';

    // Set tanggal dan waktu saat ini untuk otomatis
    $tanggal = date("Y-m-d");
    $waktu = date("H:i:s");

    // Cek apakah NIS ada di tabel siswa
    $siswa_query = "SELECT id_siswa FROM siswa WHERE nis = ?";
    $stmt = $mysqli->prepare($siswa_query);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $siswa_result = $stmt->get_result();

    if ($siswa_result->num_rows > 0) {
        // Ambil `id_siswa` berdasarkan NIS
        $siswa_data = $siswa_result->fetch_assoc();
        $id_siswa = $siswa_data['id_siswa'];

        // Ambil saldo terakhir dari siswa
        $saldo_sebelumnya_query = "SELECT saldo FROM tabungan_siswa WHERE id_siswa = ? ORDER BY id_tabungan DESC LIMIT 1";
        $stmt = $mysqli->prepare($saldo_sebelumnya_query);
        $stmt->bind_param("i", $id_siswa);
        $stmt->execute();
        $result = $stmt->get_result();
        $saldo_sebelumnya = $result->num_rows > 0 ? (float) $result->fetch_assoc()['saldo'] : 0;

        // Hitung saldo baru
        $saldo_baru = $saldo_sebelumnya + $pemasukan - $pengeluaran;

        // Masukkan data tabungan baru ke dalam database
        $insert_query = "INSERT INTO tabungan_siswa (id_siswa, tanggal, waktu, pemasukan, pengeluaran, saldo, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert_query);
        $stmt->bind_param("issddds", $id_siswa, $tanggal, $waktu, $pemasukan, $pengeluaran, $saldo_baru, $keterangan);
        
        // Eksekusi query dan cek apakah data berhasil dimasukkan
        if ($stmt->execute()) {
            echo "<script>alert('Data tabungan berhasil ditambahkan'); window.location.href='tabungan.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data tabungan'); window.location.href='tabungan.php';</script>";
        }
    } else {
        // Jika NIS tidak ditemukan
        echo "<script>alert('NIS tidak ditemukan di database.'); window.location.href='tabungan.php';</script>";
    }
}
?>
