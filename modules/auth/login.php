<?php
// modules/auth/login.php
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/../../core/functions.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    redirect('modules/dashboard/index.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kinerja Pengawas Jombang</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/image.jpg'); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url('assets/img/image.jpg'); ?>" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css?v=' . time()); ?>">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-overlay">
            <div class="auth-card">
                <div class="text-center mb-4">
                    <img src="../../assets/img/image.jpg" alt="Logo Kinerja Pengawas" width="100" class="mb-3 rounded-circle shadow">
                    <h4 class="fw-bold text-dark">Kinerja Pengawas</h4>
                    <p class="text-muted small">Kementerian Agama Kab. Jombang</p>
                </div>

                <?php echo flash(); ?>

                <form action="<?php echo base_url('modules/auth/aksi_login.php'); ?>" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nip" name="nip" placeholder="NIP" required>
                        <label for="nip">NIP / Username</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">
                        <i class="fas fa-sign-in-alt me-2"></i> Masuk Aplikasi
                    </button>
                </form>
                
                <div class="mt-4 text-center">
                    <small class="text-muted">&copy; 2026 San Project</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
