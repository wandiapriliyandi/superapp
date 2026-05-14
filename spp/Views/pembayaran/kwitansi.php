<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - <?= $no_trx ?></title>
    <style>
        @page { size: A6 portrait; margin: 0; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 11px; margin: 0; padding: 10px; color: #333; background: #f0f0f0; }
        .receipt-container { 
            width: 95mm; 
            min-height: 138mm; 
            margin: auto; 
            border: 1px solid #ddd; 
            padding: 15px; 
            background: #fff; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header { display: flex; justify-content: space-between; border-bottom: 1px solid #333; padding-bottom: 8px; margin-bottom: 12px; }
        .logo-area h3 { margin: 0; color: #1a73e8; font-size: 14px; }
        .logo-area p { margin: 1px 0; font-size: 9px; color: #666; }
        .trx-info { text-align: right; }
        .trx-info h4 { margin: 0; color: #333; font-size: 12px; }
        .trx-info p { margin: 1px 0; font-size: 9px; }
        
        .santri-info { margin-bottom: 12px; padding: 8px; background: #f9f9f9; border-radius: 4px; display: flex; gap: 20px; border: 1px solid #eee; }
        .info-group b { display: block; font-size: 8px; color: #888; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { text-align: left; padding: 6px; border-bottom: 1px solid #333; background: #f2f2f2; font-size: 10px; }
        td { padding: 6px; border-bottom: 1px solid #eee; font-size: 10px; }
        .text-end { text-align: right; }
        
        .footer { display: flex; justify-content: space-between; margin-top: 25px; }
        .signature { text-align: center; width: 120px; font-size: 10px; }
        .signature p { margin-bottom: 40px; margin-top: 0; }
        
        .terbilang-box { font-style: italic; margin-bottom: 15px; color: #555; font-size: 9px; padding: 5px; border: 1px dashed #ccc; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; background: none; }
            .receipt-container { border: none; box-shadow: none; width: 105mm; padding: 10mm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 15px; padding: 10px; background: #fff; border-bottom: 1px solid #ddd;">
        <button onclick="window.print()" style="padding: 8px 20px; background: #1a73e8; color: white; border: none; border-radius: 50px; cursor: pointer; font-weight: bold;">
            🖨️ CETAK KWITANSI (A6)
        </button>
        <a href="<?= base_url('spp/pembayaran/cari') ?>" style="padding: 8px 20px; color: #666; text-decoration: none; font-size: 13px;">Kembali</a>
    </div>

    <div class="receipt-container">
        <div class="header">
            <div class="logo-area">
                <h3>PESANTREN SUPERAPP</h3>
                <p>Jl. Raya Pesantren No. 123, Jakarta</p>
            </div>
            <div class="trx-info">
                <div style="display: flex; gap: 10px; align-items: start; justify-content: flex-end;">
                    <div style="text-align: right;">
                        <h4>KWITANSI</h4>
                        <p><b><?= $no_trx ?></b></p>
                        <p><?= date('d/m/Y', strtotime($details[0]['created_at'])) ?></p>
                    </div>
                    <div>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=<?= base_url('verify/receipt/' . $no_trx) ?>" width="60" height="60" style="border: 1px solid #eee; padding: 2px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="santri-info">
            <div class="info-group">
                <b>Diterima Dari</b>
                <span><?= $santri['nama_lengkap'] ?></span>
            </div>
            <div class="info-group">
                <b>Metode</b>
                <span><?= $details[0]['metode_pembayaran'] ?></span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                $months = [
                    0 => 'Tahunan', 1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                foreach ($details as $d): 
                    $total += $d['nominal_bayar'];
                ?>
                    <tr>
                        <td>
                            <b><?= $d['nama_tarif'] ?></b><br>
                            <small><?= $months[$d['bulan']] ?> <?= $d['tahun'] ?></small>
                        </td>
                        <td class="text-end"><?= number_format($d['nominal_bayar'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-end">TOTAL</th>
                    <th class="text-end" style="font-size: 14px;">Rp <?= number_format($total, 0, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="terbilang-box">
            Terbilang: # <?= ucwords(terbilang($total)) ?> Rupiah #
        </div>

        <div class="footer">
            <div class="signature">
                <p>Penyetor</p>
                <?php 
                    $parts = explode(' ', trim($santri['nama_lengkap']));
                    $displayName = $parts[0];
                    for ($i = 1; $i < count($parts); $i++) {
                        $displayName .= ' ' . substr($parts[$i], 0, 1) . '.';
                    }
                ?>
                ( <?= $displayName ?> )
            </div>
            <div class="signature">
                <p>Bendahara</p>
                ( <?= session()->get('user_nama') ?? 'Admin' ?> )
            </div>
        </div>
    </div>

    <script>
        // Opsional: Langsung buka dialog print
        // window.print();
    </script>
</body>
</html>

<?php 
function terbilang($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " ". $huruf[$nilai];
    } else if ($nilai <20) {
        $temp = terbilang($nilai - 10). " belas";
    } else if ($nilai < 100) {
        $temp = terbilang($nilai/10)." puluh". terbilang($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . terbilang($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = terbilang($nilai/100) . " ratus" . terbilang($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . terbilang($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = terbilang($nilai/1000) . " ribu" . terbilang($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = terbilang($nilai/1000000) . " juta" . terbilang($nilai % 1000000);
    }
    return $temp;
}
?>
