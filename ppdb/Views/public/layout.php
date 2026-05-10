<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | PPDB SuperApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8f9fc; }
        .bg-primary-custom { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
        .card { border-radius: 15px; }
        .form-control, .form-select { padding: 0.75rem 1rem; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="bg-primary-custom py-5 text-white text-center mb-5">
        <div class="container">
            <h1 class="fw-bold">Pendaftaran Santri Baru</h1>
            <p class="lead opacity-75">Selamat datang di portal pendaftaran santri baru SuperApp</p>
        </div>
    </div>

    <div class="container mb-5">
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="text-center text-muted py-4">
        <p>&copy; <?= date('Y') ?> Sistem Modular SuperApp. Hak Cipta Dilindungi Undang-Undang.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
