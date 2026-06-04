<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SuperApp Pesantren</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            overflow-x: hidden;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            perspective: 1000px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            color: #f3f4f6;
            animation: slideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes slideIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .logo-area {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-icon {
            font-size: 40px;
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            width: 80px;
            height: 80px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: transform 0.3s;
        }
        .logo-icon:hover {
            transform: scale(1.05) rotate(5deg);
        }
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f3f4f6;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            color: #fff;
        }
        .form-control::placeholder {
            color: #9ca3af;
            opacity: 0.6;
        }
        .input-group-text {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #9ca3af;
            border-radius: 12px;
        }
        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: #fff;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-radius: 12px;
            font-size: 14px;
            padding: 12px 16px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h4 class="fw-bold mb-1">SuperApp Pesantren</h4>
                <p class="text-muted small mb-0">Masukkan akun Anda untuk mengelola sistem terpadu</p>
            </div>

            <!-- Flash Message Error -->
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger mb-4 d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login/proses') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label small fw-semibold text-muted mb-2">USERNAME</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username..." value="<?= old('username') ?>" required autocomplete="off">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label small fw-semibold text-muted mb-2">PASSWORD</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password..." required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-login w-100 fw-bold">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
