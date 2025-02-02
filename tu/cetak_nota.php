<?php
require_once '../koneksi/koneksi.php';
require_once '../vendor/autoload.php'; // Pastikan path ini sesuai dengan lokasi TCPDF

// Ambil `id_nota` dari URL
$id_nota = isset($_GET['id_nota']) ? intval($_GET['id_nota']) : 0;

if ($id_nota <= 0) {
    die("Nota tidak ditemukan.");
}

// Query untuk mengambil data nota berdasarkan `id_nota`
$query = "
    SELECT n.*, s.nama_siswa, s.kelas, s.nis 
    FROM nota n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    WHERE n.id_nota = ?
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_nota);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Nota tidak ditemukan.");
}

$nota = $result->fetch_assoc();

// Daftar pembayaran
$detailPembayaranQuery = "
    SELECT bulan, tahun, jumlah 
    FROM pembayaran_spp 
    WHERE id_siswa = ? AND tanggal_bayar = ?
";
$detailStmt = $mysqli->prepare($detailPembayaranQuery);
$detailStmt->bind_param("is", $nota['id_siswa'], $nota['tanggal_pembayaran']);
$detailStmt->execute();
$detailResult = $detailStmt->get_result();

// Daftar potongan
$potongan = [];
if ($nota['jenis_potongan']) {
    if (strpos($nota['jenis_potongan'], 'Beasiswa') !== false) {
        $potongan[] = 'Beasiswa';
    }
    if (strpos($nota['jenis_potongan'], 'Potongan SPP') !== false) {
        $potongan[] = 'Potongan SPP';
    }
}

// Inisialisasi TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SD Muhammadiyah 18 Kalibaru');
$pdf->SetTitle('Bukti Pembayaran Siswa');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Konten PDF dengan CSS
$html = '
    <style>
        .header-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
           
        }
        .header-subtitle {
            text-align: center;
            font-size: 12px;
            margin-top: -40px;
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
        }
        .info-table, .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 4px;
            font-size: 12px;
        }
        .payment-table td, .payment-table th {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }
        .right-align {
            text-align: right;
        }
        .total-section {
            font-weight: bold;
        }
        .footer {
            text-align: right;
            margin-top: 20px;
        }
    </style>
    <div class="header">
        <div class="header-title">SD MUHAMMADIYAH 18 KALIBARU</div>
        <div class="header-subtitle">Jl. Seruti RT.002 RW.001 Kalibarywetan - Kalibaru</div>
        <hr>
        <div class="header-title">BUKTI PEMBAYARAN SISWA</div>
    </div>

    <table class="info-table">
        <tr>
            <td><b>Tanggal Pembayaran</b></td>
            <td>:' . date('d-m-Y', strtotime($nota['tanggal_pembayaran'])) . '</td>
            <td><b>Nama Siswa</b></td>
            <td>:' . htmlspecialchars($nota['nama_siswa']) . '</td>
        </tr>
        <tr>
            <td><b>NIS</b></td>
            <td>:' . htmlspecialchars($nota['nis']) . '</td>
            <td><b>Kelas</b></td>
            <td>:' . htmlspecialchars($nota['kelas']) . '</td>
        </tr>
    </table>
  <br>
    <div class="section-title">Keterangan Pembayaran</div>
    <br>
    <table class="payment-table">
        <tr>
            <th style="width: 80%;">Keterangan</th>
            <th style="width: 20%;" class="right-align">Jumlah</th>
        </tr>
';

$i = 1;
while ($pembayaran = $detailResult->fetch_assoc()) {
    $html .= '<tr>
                <td>' . $i . '. Pembayaran SPP Bulan ' . $pembayaran['bulan'] . ' Tahun ' . $pembayaran['tahun'] . '</td>
                <td class="right-align">Rp ' . number_format($pembayaran['jumlah'], 0, ',', '.') . '</td>
              </tr>';
    $i++;
}

$html .= '</table>';

// Potongan
$html .= '<br><div class="section-title">Potongan</div>';
if (!empty($potongan)) {
    $html .= '<table class="payment-table">';
    $j = 1;
    foreach ($potongan as $item) {
        $html .= '<tr>
                    <td style="width: 80%;">' . $j . '. ' . $item . '</td>
                    <td style="width: 20%;" class="right-align"></td>
                  </tr>';
        $j++;
    }
    $html .= '</table>';
} else {
    $html .= '<p>Tidak ada potongan.</p>';
}

// Total
$html .= '
    <table class="payment-table">
        <tr class="total-section">
            <td style="width: 80%;"><b>Total</b></td>
            <td style="width: 20%;" class="right-align"><b>Rp ' . number_format($nota['jumlah_dibayarkan'], 0, ',', '.') . '</b></td>
        </tr>
    </table>
';

// Catatan
$html .= '
    <div class="section-title"> <br>Catatan:</div>
    <div style="border: 1px solid black; padding: 5px; min-height: 50px;">
        ' . htmlspecialchars($nota['keterangan']) . '
    </div>

    <div class="footer">Kalibaru, ' . date('d F Y') . '</div>
';

// Tambahkan HTML ke PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF ke Browser (Cetak Langsung)
$pdf->Output('Bukti_Pembayaran_Siswa.pdf', 'I');
?>
