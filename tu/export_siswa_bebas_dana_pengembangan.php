<?php
session_start();
require_once '../koneksi/koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=siswa_bebas_dana_pengembangan.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Query untuk mengambil data siswa bebas dana pengembangan
$query = "
    SELECT s.nama_siswa, s.kelas, sb.alasan_bebas, sb.tanggal_mulai, sb.tanggal_selesai
    FROM siswa_bebas_dana_pengembangan sb
    JOIN siswa s ON sb.id_siswa = s.id_siswa
    ORDER BY s.nama_siswa ASC
";
$result = $mysqli->query($query);

if (!$result) {
    die("Query Error: " . $mysqli->error);
}
?>

<table border="1">
    <thead>
        <tr>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Alasan Bebas</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                <td><?php echo htmlspecialchars($row['alasan_bebas']); ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['tanggal_mulai'])); ?></td>
                <td><?php echo $row['tanggal_selesai'] ? date('d-m-Y', strtotime($row['tanggal_selesai'])) : 'Tidak ada'; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
$mysqli->close();
?>
