<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset='utf-8'>
    <title>Kesepakatan Bayaran</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        body { font-family: 'Times New Roman', serif; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; font-weight: bold; font-size: 16px; text-decoration: underline; margin-bottom: 20px; }
        .tarif-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .tarif-table th, .tarif-table td { border: 1px solid #000; padding: 5px; }
        .signature-table { width: 100%; margin-top: 50px; }
        .signature-table td { text-align: center; vertical-align: top; width: 50%; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;"><?= strtoupper($setting['pesantren_name'] ?? 'PESANTREN DIGITAL') ?></h2>
        <p style="margin:0;"><?= $setting['alamat'] ?? '' ?></p>
    </div>

    <div class="title">SURAT KESEPAKATAN PEMBAYARAN SPP & BIAYA PENDIDIKAN</div>

    <p>Yang bertanda tangan di bawah ini:</p>
    <table style="width: 100%;">
        <tr>
            <td style="width: 150px;">Nama Santri</td>
            <td>: <b><?= $santri['nama_lengkap'] ?></b></td>
        </tr>
        <tr>
            <td>NISN</td>
            <td>: <?= $santri['nisn'] ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?= $santri['alamat'] ?></td>
        </tr>
    </table>

    <p>Menyatakan setuju dan bersedia melakukan pembayaran rutin sesuai rincian berikut:</p>

    <table class="tarif-table">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th width="30">No</th>
                <th>Jenis Pembayaran</th>
                <th>Tipe</th>
                <th align="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            $total_bulanan = 0;
            $total_tahunan = 0;
            $bulanan = array_filter($mapping, function($m) { return $m['tipe'] == 'Bulanan'; });
            $tahunan = array_filter($mapping, function($m) { return $m['tipe'] == 'Tahunan'; });
            ?>

            <?php if(!empty($bulanan)): ?>
            <tr style="background-color: #eee;"><td colspan="4"><b>A. BIAYA RUTIN BULANAN</b></td></tr>
            <?php foreach ($bulanan as $m): $total_bulanan += $m['nominal']; ?>
            <tr>
                <td align="center"><?= $no++ ?></td>
                <td><?= $m['nama_tarif'] ?></td>
                <td>Bulanan</td>
                <td align="right">Rp <?= number_format($m['nominal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="font-weight: bold;">
                <td colspan="3" align="right">Subtotal Biaya Bulanan</td>
                <td align="right">Rp <?= number_format($total_bulanan, 0, ',', '.') ?></td>
            </tr>
            <?php endif; ?>

            <?php if(!empty($tahunan)): ?>
            <tr style="background-color: #eee;"><td colspan="4"><b>B. BIAYA TAHUNAN</b></td></tr>
            <?php foreach ($tahunan as $m): $total_tahunan += $m['nominal']; ?>
            <tr>
                <td align="center"><?= $no++ ?></td>
                <td><?= $m['nama_tarif'] ?></td>
                <td>Tahunan</td>
                <td align="right">Rp <?= number_format($m['nominal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="font-weight: bold;">
                <td colspan="3" align="right">Subtotal Biaya Tahunan</td>
                <td align="right">Rp <?= number_format($total_tahunan, 0, ',', '.') ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p>Demikian kesepakatan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <?php 
        $months_indo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $tgl = date('d') . ' ' . $months_indo[date('n')] . ' ' . date('Y');
    ?>

    <p align="right"><?= $setting['kabupaten'] ?? 'Jakarta' ?>, <?= $tgl ?></p>

    <table class="signature-table">
        <tr>
            <td>
                Wali Santri,<br><br><br><br>
                ( ................................ )
            </td>
            <td>
                Bendahara,<br><br><br><br>
                ( ................................ )
            </td>
        </tr>
    </table>

    <br><br>
    <table style="width: 100%; border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px;">
        <tr>
            <td style="vertical-align: middle;">
                <p style="font-size: 9px; color: #555; margin: 0;">
                    <b>Verifikasi Dokumen</b><br>
                    Scan QR Code ini or kunjungi:<br>
                    <i><?= base_url('verify/agreement/' . $santri['nisn']) ?></i><br>
                    untuk memverifikasi keaslian dokumen ini melalui sistem.
                </p>
            </td>
            <td style="text-align: right; vertical-align: middle; width: 100px;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= base_url('verify/agreement/' . $santri['nisn']) ?>" width="80" height="80" style="border: 1px solid #ddd; padding: 3px;">
            </td>
        </tr>
    </table>
</body>
</html>
