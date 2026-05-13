<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Kesepakatan Bayaran - <?= $santri['nama_lengkap'] ?></title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        body { font-family: 'Times New Roman', serif; margin: 0; padding: 20mm; color: #000; line-height: 1.5; background: #fff; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 5mm; margin-bottom: 10mm; }
        .header h1 { margin: 0; font-size: 20pt; }
        .header p { margin: 2px 0; font-size: 11pt; }
        
        .title { text-align: center; font-weight: bold; font-size: 14pt; text-decoration: underline; margin-bottom: 8mm; }
        
        .content { margin-bottom: 10mm; }
        .info-table { width: 100%; margin-bottom: 5mm; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .info-table .label { width: 150px; }
        
        .tarif-table { width: 100%; border-collapse: collapse; margin: 10mm 0; }
        .tarif-table th, .tarif-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .tarif-table th { background: #f2f2f2; }
        
        .footer { margin-top: 20mm; width: 100%; }
        .signature { display: flex; justify-content: space-between; }
        .sig-box { text-align: center; width: 200px; }
        .sig-space { height: 25mm; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1><?= strtoupper($setting['pesantren_name'] ?? 'PESANTREN DIGITAL') ?></h1>
        <p><?= $setting['alamat'] ?? 'Alamat belum diatur di pengaturan aplikasi' ?></p>
        <p>Telp: <?= $setting['telepon'] ?? '-' ?> | Email: <?= $setting['email'] ?? '-' ?></p>
    </div>

    <div class="title">SURAT KESEPAKATAN PEMBAYARAN SPP & BIAYA PENDIDIKAN</div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table class="info-table">
            <tr>
                <td class="label">Nama Santri</td>
                <td>: <b><?= $santri['nama_lengkap'] ?></b></td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td>: <?= $santri['nisn'] ?></td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td>: <?= $santri['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td>: <?= $santri['alamat'] ?></td>
            </tr>
        </table>

        <p>Menyatakan setuju dan bersedia melakukan pembayaran rutin setiap bulannya sesuai dengan rincian tarif yang telah disepakati bersama sebagai berikut:</p>

        <table class="tarif-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Jenis Pembayaran</th>
                    <th>Tipe</th>
                    <th style="text-align: right;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                $total_bulanan = 0;
                $total_tahunan = 0;
                
                // Pisahkan Bulanan
                $bulanan = array_filter($mapping, function($m) { return $m['tipe'] == 'Bulanan'; });
                $tahunan = array_filter($mapping, function($m) { return $m['tipe'] == 'Tahunan'; });
                ?>
                
                <?php if(!empty($bulanan)): ?>
                <tr style="background: #f9f9f9;"><td colspan="4"><b>A. BIAYA RUTIN BULANAN</b></td></tr>
                <?php foreach ($bulanan as $m): $total_bulanan += $m['nominal']; ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><?= $m['nama_tarif'] ?></td>
                    <td>Bulanan</td>
                    <td style="text-align: right;">Rp <?= number_format($m['nominal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr style="font-weight: bold;">
                    <td colspan="3" style="text-align: right;">Subtotal Biaya Bulanan</td>
                    <td style="text-align: right;">Rp <?= number_format($total_bulanan, 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>

                <?php if(!empty($tahunan)): ?>
                <tr><td colspan="4" style="border:none; height: 10px;"></td></tr>
                <tr style="background: #f9f9f9;"><td colspan="4"><b>B. BIAYA TAHUNAN</b></td></tr>
                <?php foreach ($tahunan as $m): $total_tahunan += $m['nominal']; ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><?= $m['nama_tarif'] ?></td>
                    <td>Tahunan</td>
                    <td style="text-align: right;">Rp <?= number_format($m['nominal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr style="font-weight: bold;">
                    <td colspan="3" style="text-align: right;">Subtotal Biaya Tahunan</td>
                    <td style="text-align: right;">Rp <?= number_format($total_tahunan, 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p>Demikian kesepakatan ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer">
        <?php 
            $months_indo = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $tgl = date('d') . ' ' . $months_indo[date('n')] . ' ' . date('Y');
        ?>
        <p style="text-align: right; margin-bottom: 10mm;"><?= $setting['kabupaten'] ?? 'Jakarta' ?>, <?= $tgl ?></p>
        <div class="signature">
            <div class="sig-box">
                <p>Wali Santri,</p>
                <div class="sig-space"></div>
                <p><b>( ................................ )</b></p>
            </div>
            <div class="sig-box">
                <p>Bendahara,</p>
                <div class="sig-space"></div>
                <p><b>( ................................ )</b></p>
            </div>
        </div>
    </div>

    <div class="no-print" style="margin-top: 50px; text-align: center;">
        <button onclick="window.print()">Cetak Surat</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html>
