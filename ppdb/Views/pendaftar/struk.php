<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran - <?= $p['nomor_kwitansi'] ?></title>
    <style>
        @page { size: A5; margin: 0; }
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 10mm; font-size: 13px; color: #333; }
        .container { border: 2px solid #333; padding: 10mm; height: 160mm; position: relative; }
        .header { display: flex; border-bottom: 2px solid #333; padding-bottom: 5mm; margin-bottom: 5mm; }
        .logo { width: 60px; height: 60px; background: #333; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; border-radius: 5px; margin-right: 15px; }
        .header-text h2 { margin: 0; color: #1a5928; }
        .header-text p { margin: 2px 0 0; font-size: 11px; }
        
        .title { text-align: center; text-decoration: underline; font-size: 18px; font-weight: bold; margin: 5mm 0; }
        
        table { width: 100%; border-collapse: collapse; margin: 5mm 0; }
        table td { padding: 8px 0; vertical-align: top; }
        .label { width: 150px; }
        .colon { width: 20px; }
        
        .amount-box { border: 2px solid #333; padding: 10px 20px; display: inline-block; font-size: 18px; font-weight: bold; background: #f8f9fa; margin: 5mm 0; }
        
        .footer { margin-top: 10mm; display: flex; justify-content: flex-end; }
        .signature { text-align: center; width: 200px; }
        .signature p { margin-bottom: 60px; }
        .signature b { border-top: 1px solid #333; padding-top: 5px; display: block; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <?php 
        $db = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();
        $logo = base_url('assets/img/logo.png');
        if($setting && $setting['app_logo'] && file_exists(FCPATH . 'uploads/img/' . $setting['app_logo'])) {
            $logo = base_url('uploads/img/' . $setting['app_logo']);
        }
    ?>
    <div class="container">
        <div class="header">
            <img src="<?= $logo ?>" alt="Logo" style="width: 60px; height: 60px; margin-right: 15px; object-fit: contain;">
            <div class="header-text">
                <h2><?= strtoupper($setting['pesantren_name'] ?? 'PESANTREN MODERN SUPERAPP') ?></h2>
                <p><?= $setting['alamat'] ?? 'Jl. Pendidikan No. 123, Kota Santri, Indonesia' ?><br>
                   Telp: <?= $setting['telepon'] ?? '-' ?> | Email: <?= $setting['email'] ?? '-' ?></p>
            </div>
        </div>

        <div class="title">KWITANSI PEMBAYARAN</div>

        <table>
            <tr>
                <td class="label">Nomor Kwitansi</td>
                <td class="colon">:</td>
                <td style="font-weight: bold;"><?= $p['nomor_kwitansi'] ?></td>
            </tr>
            <tr>
                <td class="label">Telah Terima Dari</td>
                <td class="colon">:</td>
                <td><?= $p['nama_lengkap'] ?> (<?= $p['nomor_pendaftaran'] ?>)</td>
            </tr>
            <tr>
                <td class="label">Tanggal Bayar</td>
                <td class="colon">:</td>
                <td><?= date('d F Y', strtotime($p['tanggal_bayar'])) ?></td>
            </tr>
            <tr>
                <td class="label">Metode Pembayaran</td>
                <td class="colon">:</td>
                <td><?= $p['metode_bayar'] ?></td>
            </tr>
            <tr>
                <td class="label">Untuk Pembayaran</td>
                <td class="colon">:</td>
                <td>Biaya Pendaftaran Santri Baru Tahun Ajaran 2026/2027<br><small><i><?= $p['keterangan'] ?></i></small></td>
            </tr>
        </table>

        <div class="amount-box">
            Rp <?= number_format($p['jumlah'], 0, ',', '.') ?>,-
        </div>

        <div class="footer">
            <div style="flex-grow: 1; display: flex; align-items: flex-end;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= $p['nomor_kwitansi'] ?>" alt="QR Code" style="border: 1px solid #ddd; padding: 2px;">
            </div>
            <div class="signature">
                <p>Panitia PPDB,<br><?= date('d F Y') ?></p>
                <b>( Bendahara Penerimaan )</b>
            </div>
        </div>

        <div style="position: absolute; bottom: 5mm; left: 10mm; font-size: 10px; color: #777; font-style: italic;">
            * Dicetak otomatis melalui Sistem Informasi Pesantren (<?= $setting['app_name'] ?? 'SuperApp' ?>) pada <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 25px; cursor: pointer;">Cetak Kwitansi</button>
        <button onclick="window.close()" style="padding: 10px 25px; cursor: pointer;">Tutup</button>
    </div>
</body>
</html>
