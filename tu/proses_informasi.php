<?php
session_start();
require_once '../koneksi/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $recipient = $_POST['recipient'];
    $pengirim_id = $_SESSION['id_admin']; // Ambil ID admin dari session
    
    // Simpan file attachment jika ada
    $file_lampiran = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = basename($_FILES['attachment']['name']);
        $destination = '../uploads/' . $file_name;
        if (move_uploaded_file($file_tmp, $destination)) {
            $file_lampiran = $destination;
        }
    }

    // Masukkan data ke tabel `informasi`, termasuk `file_lampiran`
    $untuk_semua = ($recipient === 'all') ? 1 : 0;
    $query = "INSERT INTO informasi (id_pengirim, judul, isi, untuk_semua, file_lampiran) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("issis", $pengirim_id, $judul, $isi, $untuk_semua, $file_lampiran);
    $stmt->execute();
    $informasi_id = $stmt->insert_id;

    // Masukkan data penerima sesuai tujuan
    if ($untuk_semua) {
        $siswa_query = "SELECT id_siswa FROM siswa";
        $siswa_result = $mysqli->query($siswa_query);
        while ($siswa = $siswa_result->fetch_assoc()) {
            $insert_penerima_query = "INSERT INTO informasi_penerima (id_informasi, id_siswa) VALUES (?, ?)";
            $stmt = $mysqli->prepare($insert_penerima_query);
            $stmt->bind_param("ii", $informasi_id, $siswa['id_siswa']);
            $stmt->execute();
        }
    } elseif ($recipient === 'selected' && isset($_POST['selected_students'])) {
        foreach ($_POST['selected_students'] as $id_siswa) {
            $insert_penerima_query = "INSERT INTO informasi_penerima (id_informasi, id_siswa) VALUES (?, ?)";
            $stmt = $mysqli->prepare($insert_penerima_query);
            $stmt->bind_param("ii", $informasi_id, $id_siswa);
            $stmt->execute();
        }
    } elseif ($recipient === 'single' && isset($_POST['selected_students'][0])) {
        $id_siswa = $_POST['selected_students'][0];
        $insert_penerima_query = "INSERT INTO informasi_penerima (id_informasi, id_siswa) VALUES (?, ?)";
        $stmt = $mysqli->prepare($insert_penerima_query);
        $stmt->bind_param("ii", $informasi_id, $id_siswa);
        $stmt->execute();
    }

    echo "<script>alert('Informasi berhasil dikirim.'); window.location.href='informasi.php';</script>";
    exit();
}
?>
