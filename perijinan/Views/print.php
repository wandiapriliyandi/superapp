<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin - <?= $rombongan[0]['token'] ?? 'SuperApp' ?></title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            font-size: 10px;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .print-area {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .print-card {
            background-color: white;
            width: 105mm;
            height: 148mm;
            padding: 7mm 6mm;
            box-sizing: border-box;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #e5e7eb;
            page-break-after: always;
        }
        .kop-surat {
            border-bottom: 2px double #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
            text-align: center;
        }
        .kop-surat h4 {
            font-size: 11px;
            margin: 0;
            font-weight: 700;
            text-transform: uppercase;
            color: #0d6efd;
        }
        .kop-surat h6 {
            font-size: 8px;
            margin: 1px 0;
            font-weight: 600;
            color: #374151;
        }
        .kop-surat p {
            font-size: 6.5px;
            margin: 0;
            color: #6b7280;
        }
        .title-surat {
            font-size: 9px;
            font-weight: 700;
            text-align: center;
            text-decoration: underline;
            margin-top: 2px;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .santri-info {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 6px;
            padding: 5px 8px;
            margin-bottom: 6px;
        }
        .rincian-izin {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 6px 8px;
            margin-bottom: 8px;
        }
        .rincian-row {
            display: flex;
            margin-bottom: 3px;
        }
        .rincian-row:last-child {
            margin-bottom: 0;
        }
        .rincian-label {
            width: 35%;
            color: #4b5563;
            font-weight: 500;
        }
        .rincian-value {
            width: 65%;
            font-weight: 600;
            color: #111827;
        }
        .ttd-section {
            display: flex;
            justify-content: space-between;
            text-align: center;
            font-size: 7.5px;
            margin-bottom: 8px;
        }
        .ttd-box {
            width: 45%;
        }
        .ttd-space {
            height: 25px;
        }
        .token-section {
            border-top: 1px dashed #d1d5db;
            padding-top: 6px;
            margin-bottom: 4px;
        }
        .notes {
            font-size: 6px;
            color: #9ca3af;
            line-height: 1.2;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 4px;
            margin-top: auto;
        }
        @page {
            size: A6 portrait;
            margin: 0;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
                display: block;
            }
            .print-area {
                gap: 0;
            }
            .print-card {
                width: 105mm;
                height: 148mm;
                padding: 7mm 6mm;
                box-shadow: none;
                border-radius: 0;
                border: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Tombol Aksi di Luar Cetak -->
    <div class="no-print d-flex gap-2 justify-content-center mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4 py-2 rounded-pill"><i class="bi bi-printer me-2"></i> Cetak Surat</button>
        <button onclick="window.close()" class="btn btn-light btn-sm px-4 py-2 rounded-pill border">Tutup</button>
    </div>

    <div class="print-area">
        <?php foreach ($rombongan as $r): ?>
        <div class="print-card">
            
            <!-- Kop Surat -->
            <div class="kop-surat">
                <h4><?= esc($setting['app_name'] ?? 'SuperApp Pesantren') ?></h4>
                <h6><?= esc($setting['pesantren_name'] ?? 'Pesantren Modern Digital') ?></h6>
                <p>Sistem Informasi Izin Keluar Masuk Santri Terintegrasi</p>
            </div>

            <!-- Judul Surat (Center) -->
            <div class="title-surat">
                SURAT IZIN KELUAR SANTRI
            </div>

            <!-- Informasi Santri -->
            <div class="santri-info">
                <div class="row g-1">
                    <div class="col-3 text-muted" style="font-size: 7.5px;">Nama Santri</div>
                    <div class="col-9 fw-bold text-primary" style="font-size: 8px;">: <?= esc($r['nama_santri']) ?></div>
                    <div class="col-3 text-muted" style="font-size: 7.5px;">NIS/NISN</div>
                    <div class="col-9 fw-semibold" style="font-size: 8px;">: <?= esc($r['nis'] ?: '-') ?> / <?= esc($r['nisn'] ?: '-') ?></div>
                </div>
            </div>

            <!-- Rincian Izin -->
            <div class="rincian-izin">
                <div class="rincian-row">
                    <div class="rincian-label">Jenis Izin</div>
                    <div class="rincian-value text-primary">: <?= esc($r['jenis_izin']) ?></div>
                </div>
                <div class="rincian-row">
                    <div class="rincian-label">Alasan Izin</div>
                    <div class="rincian-value">: <?= esc($r['alasan'] ?: '-') ?></div>
                </div>
                <div class="rincian-row">
                    <div class="rincian-label">Waktu Mulai</div>
                    <div class="rincian-value">: <?= date('d M Y H:i', strtotime($r['tanggal_mulai'])) ?> WIB</div>
                </div>
                <div class="rincian-row">
                    <div class="rincian-label">Batas Kembali</div>
                    <div class="rincian-value text-danger">: <?= date('d M Y H:i', strtotime($r['tanggal_selesai'])) ?> WIB</div>
                </div>
            </div>

            <!-- Tanda Tangan -->
            <div class="ttd-section">
                <div class="ttd-box">
                    <p class="mb-0">Santri Bersangkutan,</p>
                    <div class="ttd-space"></div>
                    <p class="fw-bold mb-0 text-decoration-underline"><?= esc($r['nama_santri']) ?></p>
                </div>
                <div class="ttd-box">
                    <p class="mb-0">Petugas Penjaga,</p>
                    <div class="ttd-space"></div>
                    <p class="fw-bold mb-0 text-decoration-underline">___________________</p>
                </div>
            </div>

            <!-- Token & QR Code Section (Setelah TTD) -->
            <div class="token-section d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted" style="font-size: 7px;">Token Izin Resmi:</span><br>
                    <span class="fw-bold bg-dark text-white px-2 py-0_5 rounded font-monospace" style="letter-spacing: 0.5px; font-size: 8px;"><?= esc($r['token'] ?? '-') ?></span>
                </div>
                <div>
                    <?php if (!empty($r['token'])): ?>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= $r['token'] ?>" alt="QR Code Token" class="img-thumbnail p-0 border-0" width="38" height="38">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Catatan Kaki (Di paling bawah kertas) -->
            <div class="notes">
                * Santri wajib melapor ke pos penjagaan keamanan/kesantrian setibanya di lingkungan pesantren untuk mengonfirmasi kepulangan dan memindai token ini.
            </div>

        </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
