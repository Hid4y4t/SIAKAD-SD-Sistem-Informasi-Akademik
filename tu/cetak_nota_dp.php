<?php
require_once '../koneksi/koneksi.php';
require_once '../vendor/autoload.php'; // Pastikan path ke TCPDF sudah benar

// Ambil `id_history` dari URL
$id_history = isset($_GET['id_history']) ? intval($_GET['id_history']) : 0;

if ($id_history <= 0) {
    die("Nota tidak ditemukan.");
}

// Query untuk mendapatkan data nota dari `dana_pengembangan_history`
$query = "
    SELECT h.*, s.nama_siswa, s.kelas, s.nis, dp.total_tagihan, dp.jumlah_terbayar
    FROM dana_pengembangan_history h
    JOIN dana_pengembangan dp ON h.id_dana_pengembangan = dp.id_dana_pengembangan
    JOIN siswa s ON dp.id_siswa = s.id_siswa
    WHERE h.id_history = ?
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_history);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Nota tidak ditemukan.");
}

$nota = $result->fetch_assoc();
$sisa_tagihan = max(0, $nota['total_tagihan'] - $nota['jumlah_terbayar']);
$status = $sisa_tagihan > 0 ? 'Belum Lunas' : 'Lunas';

// Inisialisasi TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SD Muhammadiyah 18 Kalibaru');
$pdf->SetTitle('Nota Pembayaran Dana Pengembangan');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Desain Nota PDF
$html = '
    <div style="text-align: center; font-family: Arial, sans-serif;">
        <h1 style="color: #003366; font-size: 24px;">SD MUHAMMADIYAH 18 KALIBARU</h1>
        <p style="margin: 0; font-size: 14px;">Jl. Seruti RT.002 RW.001 Kalibaruwetan - Kalibaru</p>
       
        <h2 style="background-color: #f2f2f2; padding: 8px; border-radius: 5px; font-size: 20px;">NOTA PEMBAYARAN DANA PENGEMBANGAN</h2>
    </div>

    <!-- Info Utama dalam tabel tanpa border -->
    <table style="width: 100%; font-size: 11px;" cellpadding="4">
        <tr>
            <td style="width: 20%;"><strong>Tanggal Bayar</strong></td>
            <td style="width: 30%;">:' . date('d-m-Y', strtotime($nota['tanggal_bayar'])) . '</td>
            <td style="width: 18%;"><strong>Nama Siswa</strong></td>
            <td style="width: 32%;">:' . htmlspecialchars($nota['nama_siswa']) . '</td>
        </tr>
        <tr>
            <td style="width: 20%;"><strong>NIS</strong></td>
            <td style="width: 30%;">:' . htmlspecialchars($nota['nis']) . '</td>
            <td style="width: 18%;"><strong>Kelas</strong></td>
            <td style="width: 32%;">:' . htmlspecialchars($nota['kelas']) . '</td>
        </tr>
    </table>
    
    <hr style="border-top: 1px solid #333; margin-top: 20px;">

    <!-- Info Pembayaran -->
    <table style="width: 100%; font-size: 14px;">
        <tr>
            <td style="width: 70%;"><strong>Jumlah Pembayaran</strong></td>
            <td style="width: 30%;">: Rp ' . number_format($nota['jumlah_bayar'], 0, ',', '.') . '</td>
        </tr>
        <tr>
            <td style="width: 70%;"><strong>Metode Pembayaran</strong></td>
            <td style="width: 30%;">:' . htmlspecialchars($nota['metode_pembayaran']) . '</td>
        </tr>
        <tr>
            <td style="width: 70%;"><strong>Sisa Tagihan</strong></td>
            <td style="width: 30%; color: ' . ($sisa_tagihan > 0 ? '#FF0000' : '#008000') . ';">
                :' . ($sisa_tagihan > 0 ? 'Rp ' . number_format($sisa_tagihan, 0, ',', '.') : 'Lunas') . '
            </td>
        </tr>
    </table>

    <hr style="margin-top: 20px; border-top: 1px solid #333;">
    
    <div style="text-align: right; margin-top: 20px;">
        <p>Kalibaru, ' . date('d F Y') . '</p>
        <br>
        <p><strong>Administrasi SD Muhammadiyah 18 Kalibaru</strong></p>
    </div>
';

// Tambahkan HTML ke PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF ke Browser
$pdf->Output('Nota_Pembayaran_Dana_Pengembangan.pdf', 'I');
?>
