<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_siswa = isset($_POST['id_siswa']) ? intval($_POST['id_siswa']) : 0;
    $jumlah_penarikan = isset($_POST['jumlah_penarikan']) ? (float) $_POST['jumlah_penarikan'] : 0.0;
    $keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';

    // Validasi data
    if ($id_siswa <= 0 || $jumlah_penarikan <= 0) {
        echo "<script>alert('ID Siswa atau jumlah penarikan tidak valid.'); window.location.href='tabungan.php';</script>";
        exit;
    }

    // Ambil saldo terakhir dari siswa
    $saldo_sebelumnya_query = "SELECT saldo FROM tabungan_siswa WHERE id_siswa = ? ORDER BY id_tabungan DESC LIMIT 1";
    $stmt = $mysqli->prepare($saldo_sebelumnya_query);
    $stmt->bind_param("i", $id_siswa);
    $stmt->execute();
    $result = $stmt->get_result();
    $saldo_sebelumnya = $result->num_rows > 0 ? (float) $result->fetch_assoc()['saldo'] : 0.0;

    // Periksa apakah saldo mencukupi
    if ($saldo_sebelumnya < $jumlah_penarikan) {
        echo "<script>alert('Saldo tidak mencukupi untuk penarikan.'); window.location.href='tabungan.php';</script>";
        exit;
    }

    // Hitung saldo baru setelah penarikan
    $saldo_baru = $saldo_sebelumnya - $jumlah_penarikan;

    // Masukkan data penarikan baru ke dalam tabel tabungan_siswa
    $insert_query = "INSERT INTO tabungan_siswa (id_siswa, tanggal, waktu, pemasukan, pengeluaran, saldo, keterangan) VALUES (?, CURDATE(), CURTIME(), 0, ?, ?, ?)";
    $stmt = $mysqli->prepare($insert_query);
    $stmt->bind_param("idds", $id_siswa, $jumlah_penarikan, $saldo_baru, $keterangan);

    // Eksekusi dan cek apakah data berhasil disimpan
    if ($stmt->execute()) {
        echo "<script>alert('Penarikan berhasil ditambahkan.'); window.location.href='tabungan.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan penarikan.'); window.location.href='tabungan.php';</script>";
    }

    // Tutup statement
    $stmt->close();
} else {
    echo "<script>alert('Metode pengiriman tidak valid.'); window.location.href='tabungan.php';</script>";
}

// Tutup koneksi
$mysqli->close();
?>
