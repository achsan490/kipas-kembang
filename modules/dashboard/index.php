<?php
// modules/dashboard/index.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// QUERY DASHBOARD
if ($role == 'pengawas') {
    // Statisik Pengawas
    $q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja WHERE user_id = $user_id");
    $total = mysqli_fetch_assoc($q_total)['total'];
    
    $q_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja WHERE user_id = $user_id AND status = 'pending'");
    $pending = mysqli_fetch_assoc($q_pending)['total'];
    
    $q_approved = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja WHERE user_id = $user_id AND status = 'disetujui'");
    $approved = mysqli_fetch_assoc($q_approved)['total'];
} else {
    // Statistik Pimpinan / Admin (Global)
    $q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja");
    $total = mysqli_fetch_assoc($q_total)['total'];
    
    $q_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja WHERE status = 'pending'");
    $pending = mysqli_fetch_assoc($q_pending)['total'];
    
    $q_user = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'pengawas'");
    $total_pengawas = mysqli_fetch_assoc($q_user)['total'];
}
?>

<main class="main-content w-100">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Dashboard Ikhtisar</h4>
                        <p class="text-secondary mb-0">Selamat Datang, <strong><?php echo $user['nama']; ?></strong>!</p>
                    </div>
                    <div class="d-none d-md-block">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            <i class="far fa-calendar-alt me-2"></i> <?php echo date('d M Y'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo flash(); ?>

    <div class="row g-4">
        <?php if ($role == 'pengawas'): ?>
        <!-- Card Pengawas -->
        <div class="col-md-4">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #004d40 0%, #00796b 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="card-title text-white-50 small text-uppercase fw-bold">Total Laporan</h5>
                    <h2 class="display-4 fw-bold mb-0 mt-2 text-white"><?php echo $total; ?></h2>
                    <i class="fas fa-file-alt stat-card-icon text-white"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="card-title text-white-50 small text-uppercase fw-bold">Menunggu Validasi</h5>
                    <h2 class="display-4 fw-bold mb-0 mt-2 text-white"><?php echo $pending; ?></h2>
                    <i class="fas fa-hourglass-half stat-card-icon text-white"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #00acc1 0%, #0097a7 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="card-title text-white-50 small text-uppercase fw-bold">Disetujui</h5>
                    <h2 class="display-4 fw-bold mb-0 mt-2 text-white"><?php echo $approved; ?></h2>
                    <i class="fas fa-check-circle stat-card-icon text-white"></i>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Card Pimpinan / Admin -->
        <div class="col-md-4">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="card-title text-white-50 small text-uppercase fw-bold">Perlu Validasi</h5>
                    <h2 class="display-4 fw-bold mb-0 mt-2 text-white"><?php echo $pending; ?></h2>
                    <p class="mb-0 mt-2 text-white-50">Laporan menunggu tindakan</p>
                    <i class="fas fa-clipboard-check stat-card-icon text-white"></i>
                    <a href="<?php echo base_url('modules/validasi/index.php'); ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
         <div class="col-md-4">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #00acc1 0%, #0097a7 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="card-title text-white-50 small text-uppercase fw-bold">Total Pengawas</h5>
                    <h2 class="display-4 fw-bold mb-0 mt-2 text-white"><?php echo $total_pengawas; ?></h2>
                    <p class="mb-0 mt-2 text-white-50">User aktif dalam sistem</p>
                    <i class="fas fa-users stat-card-icon text-white"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="card-title text-white-50 small text-uppercase fw-bold">Total Kinerja</h5>
                    <h2 class="display-4 fw-bold mb-0 mt-2 text-white"><?php echo $total; ?></h2>
                    <p class="mb-0 mt-2 text-white-50">Akumulasi seluruh data</p>
                    <i class="fas fa-database stat-card-icon text-white"></i>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
