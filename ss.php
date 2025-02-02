<?php
// Mengimpor file koneksi
include 'koneksi.php';

// Query untuk mengambil data dari tabel 'produk'
$sql = "SELECT id_produk, nama_produk, harga, stok FROM produk";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
   
</head>
<body>
    <h1>Data Produk</h1>

    <table>
        <tr>
            <th>ID Produk</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stok</th>
        </tr>
        <?php
        // Mengecek jika data ditemukan
        if ($result->num_rows > 0) {
            // Menampilkan data per baris
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row["id_produk"]; ?></td>
                    
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="4">Tidak ada data ditemukan</td>
            </tr>
            <?php
        }
        ?>
    </table>

</body>
</html>

<?php
// Menutup koneksi
$conn->close();
?>
