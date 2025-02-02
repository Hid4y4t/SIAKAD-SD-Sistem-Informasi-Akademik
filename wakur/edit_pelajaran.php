<?php
require_once '../koneksi/koneksi.php';

// Periksa apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    if (!isset($_POST['id_pelajaran'], $_POST['nama_pelajaran'], $_POST['id_guru']) || 
        empty($_POST['id_pelajaran']) || 
        empty($_POST['nama_pelajaran']) || 
        empty($_POST['id_guru'])) {
        echo "<script>
                alert('Data tidak valid! Harap lengkapi semua field.');
                window.location.href = 'mapel.php';
              </script>";
        exit;
    }

    $id_pelajaran = $_POST['id_pelajaran'];
    $nama_pelajaran = $_POST['nama_pelajaran'];
    $id_guru = $_POST['id_guru'];

    // Update data pelajaran
    $query = "UPDATE pelajaran SET nama_pelajaran = ?, id_guru = ? WHERE id_pelajaran = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sii", $nama_pelajaran, $id_guru, $id_pelajaran);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data pelajaran berhasil diperbarui!');
                window.location.href = 'mapel.php';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan saat memperbarui data!');
                window.location.href = 'mapel.php';
              </script>";
    }

    $stmt->close();
    $mysqli->close();
} else {
    // Jika bukan request POST
    echo "<script>
            alert('Akses tidak valid!');
            window.location.href = 'mapel.php';
          </script>";
    exit;
}
?>
