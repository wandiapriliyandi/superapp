<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
    <?= $print_logic ?>
</head>
<body>
    <div class="header">
        <h2><?= $title ?></h2>
        <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Lengkap</th>
                <th>JK</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($santri as $s): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $s['nisn'] ?></td>
                <td><?= $s['nama_lengkap'] ?></td>
                <td><?= $s['jenis_kelamin'] ?></td>
                <td><?= $s['status_santri'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
