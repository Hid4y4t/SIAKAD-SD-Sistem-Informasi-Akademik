<?php
// hapus_jenis_potongan.php

require '../koneksi/koneksi.php';

// Cek apakah ID potongan sudah diterima melalui metode POST
if (isset($_POST['id_potongan'])) {
    // Ambil ID potongan
    $id_potongan = $mysqli->real_escape_string($_POST['id_potongan']);

    // Query untuk menghapus data dari tabel jenis_potongan_spp
    $query = "DELETE FROM jenis_potongan_spp WHERE id_potongan = '$id_potongan'";

    // Eksekusi query dan cek apakah berhasil
    if ($mysqli->query($query) === TRUE) {
        echo "<script>alert('Jenis potongan berhasil dihapus'); window.location.href='potongan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus jenis potongan: " . $mysqli->error . "'); window.location.href='potongan.php';</script>";
    }
} else {
    echo "<script>alert('ID potongan tidak ditemukan'); window.location.href='potongan.php';</script>";
}

// Tutup koneksi
$mysqli->close();
?>
