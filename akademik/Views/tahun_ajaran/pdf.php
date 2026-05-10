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
        <p>Data Master Tahun Ajaran SuperApp</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun Ajaran</th>
                <th>Semester</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($ta as $item): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $item['tahun_ajaran'] ?></td>
                <td><?= $item['semester'] ?></td>
                <td><?= $item['tgl_mulai'] ?></td>
                <td><?= $item['tgl_selesai'] ?></td>
                <td><?= $item['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
