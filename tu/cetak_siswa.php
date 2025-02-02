<?php
require '../koneksi/koneksi.php';

// Ambil parameter ids dari URL
$ids = isset($_GET['ids']) ? explode(",", $_GET['ids']) : [];

if (empty($ids)) {
    echo "Tidak ada data yang dipilih untuk dicetak.";
    exit;
}

// Buat query untuk mengambil data siswa berdasarkan NIS yang dipilih
$idPlaceholders = implode(",", array_fill(0, count($ids), "?"));
$query = "SELECT * FROM siswa WHERE nis IN ($idPlaceholders)";
$stmt = $mysqli->prepare($query);

// Bind parameter untuk NIS
$stmt->bind_param(str_repeat("s", count($ids)), ...$ids);
$stmt->execute();
$result = $stmt->get_result();

// HTML dan CSS untuk tampilan yang lebih baik
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #4CAF50;
            font-size: 24px;
        }
        .table-container {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .table-container th, .table-container td {
            padding: 10px;
            text-align: left;
        }
        .table-container th {
            background-color: #4CAF50;
            color: white;
        }
        .table-container tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table-container tr:hover {
            background-color: #ddd;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .status-aktif {
            background-color: #4CAF50;
        }
        .status-off {
            background-color: #f44336;
        }
        .status-pindah {
            background-color: #ff9800;
        }
    </style>
</head>
<body>

    <h2>Data Siswa</h2>
    <table class="table-container" border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Tanggal Lahir</th>
            <th>Jenis Kelamin</th>
            <th>Status</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['nis']) ?></td>
                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                <td><?= htmlspecialchars($row['kelas']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_lahir']) ?></td>
                <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                <td>
                    <span class="status-badge <?= $row['status'] == 1 ? 'status-aktif' : ($row['status'] == 0 ? 'status-off' : 'status-pindah') ?>">
                        <?= $row['status'] == 1 ? 'Aktif' : ($row['status'] == 0 ? 'Off' : 'Pindah') ?>
                    </span>
                </td>
            </tr>
        <?php } ?>
    </table>

    <script>
        window.print(); // Cetak otomatis saat halaman dibuka
    </script>

</body>
</html>
