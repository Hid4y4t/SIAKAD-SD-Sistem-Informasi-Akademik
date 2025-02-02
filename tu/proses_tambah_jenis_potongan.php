<?php
// proses_tambah_jenis_potongan.php

require '../koneksi/koneksi.php';

// Cek apakah data yang diperlukan sudah dikirim melalui POST
if (isset($_POST['nama_potongan']) && isset($_POST['potongan'])) {
    // Ambil data dari formulir
    $nama_potongan = $mysqli->real_escape_string($_POST['nama_potongan']);
    $keterangan = isset($_POST['keterangan']) ? $mysqli->real_escape_string($_POST['keterangan']) : null;
    $potongan = $mysqli->real_escape_string($_POST['potongan']);

    // Query untuk menambahkan data ke tabel jenis_potongan_spp
    $query = "INSERT INTO jenis_potongan_spp (nama_potongan, keterangan, potongan) VALUES ('$nama_potongan', '$keterangan', '$potongan')";

    // Eksekusi query dan cek apakah berhasil
    if ($mysqli->query($query) === TRUE) {
        echo "<script>alert('Jenis potongan berhasil ditambahkan'); window.location.href='potongan.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jenis potongan: " . $mysqli->error . "'); window.location.href='potongan.php';</script>";
    }
} else {
    echo "<script>alert('Data tidak lengkap'); window.location.href='potongan.php';</script>";
}

// Tutup koneksi
$mysqli->close();
?>
