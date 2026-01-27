<?php
// modules/dashboard/index.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// QUERY DASHBOARD
if ($role == 'pengawas') {
    // Statistik Pengawas
    $q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja WHERE user_id = $user_id");
    $total = mysqli_fetch_assoc($q_total)['total'];
    
    $q_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja WHERE user_id = $user_id AND status = 'pending'");
    $pending = mysqli_fetch_assoc($q_pending)['total'];
    
    $q_approved = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja WHERE user_id = $user_id AND status = 'disetujui'");
    $approved = mysqli_fetch_assoc($q_approved)['total'];
} else {
    // Statistik Pimpinan / Admin (Global) - ENHANCED
    $q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM kinerja");
    $total = mysqli_fetch_assoc($q_total)['total'];
    
    $q_user = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'pengawas'");
    $total_pengawas = mysqli_fetch_assoc($q_user)['total'];
    
    $q_madrasah = mysqli_query($conn, "SELECT COUNT(*) as total FROM madrasah");
    $total_madrasah = mysqli_fetch_assoc($q_madrasah)['total'];
    
    // Get advanced analytics data
    $trend = getKinerjaTrend($conn);
    $monthlyData = getKinerjaMonthly($conn);
    $jenjangData = getKinerjaByJenjang($conn);
    $kecamatanData = getKinerjaByKecamatan($conn);
    $topPengawas = getTopPengawas($conn, 10);
}
?>

<main class="main-content w-100">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-white p-4 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">
                            <i class="fas fa-chart-line me-2 text-primary"></i>
                            Dashboard Analytics
                        </h4>
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

    <?php if ($role == 'pengawas'): ?>
    <!-- DASHBOARD PENGAWAS (Existing) -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #004d40 0%, #00796b 100%); color: white;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-white-50 small text-uppercase fw-bold">Total Laporan Kinerja</h5>
                        <h2 class="display-4 fw-bold mb-0 mt-2 text-white"><?php echo $total; ?></h2>
                    </div>
                    <div>
                         <i class="fas fa-file-alt fa-4x text-white opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- DASHBOARD PIMPINAN/ADMIN - ENHANCED WITH ANALYTICS -->
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-white-50 small text-uppercase fw-bold mb-2">Total Kinerja</h6>
                            <h2 class="display-5 fw-bold mb-1 text-white"><?php echo $total; ?></h2>
                            <?php if ($trend['percentage'] != 0): ?>
                            <span class="badge" style="background: rgba(255,255,255,0.2);">
                                <i class="fas fa-arrow-<?php echo $trend['trend'] == 'up' ? 'up' : 'down'; ?> me-1"></i>
                                <?php echo abs($trend['percentage']); ?>% bulan ini
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line fa-2x text-white opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #00acc1 0%, #0097a7 100%); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-white-50 small text-uppercase fw-bold mb-2">Total Pengawas</h6>
                            <h2 class="display-5 fw-bold mb-1 text-white"><?php echo $total_pengawas; ?></h2>
                            <span class="text-white-50 small">User aktif</span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users fa-2x text-white opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-white-50 small text-uppercase fw-bold mb-2">Total Madrasah</h6>
                            <h2 class="display-5 fw-bold mb-1 text-white"><?php echo $total_madrasah; ?></h2>
                            <span class="text-white-50 small">Kab. Jombang</span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-school fa-2x text-white opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-white-50 small text-uppercase fw-bold mb-2">Rata-rata/Bulan</h6>
                            <h2 class="display-5 fw-bold mb-1 text-white"><?php echo round($total / 12); ?></h2>
                            <span class="text-white-50 small">Kinerja/bulan</span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check fa-2x text-white opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Line Chart - Kinerja Bulanan -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-area text-primary me-2"></i>
                        Trend Kinerja 12 Bulan Terakhir
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px;">
                        <canvas id="chartKinerjaMonthly"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bar Chart - Kinerja per Jenjang -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                        Per Jenjang
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px;">
                        <canvas id="chartKinerjaJenjang"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart & Top Pengawas Row -->
    <div class="row g-4">
        <!-- Pie Chart - Kinerja per Kecamatan -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-map-marked-alt text-primary me-2"></i>
                        Top 10 Kecamatan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 350px;">
                        <canvas id="chartKinerjaKecamatan"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top 10 Pengawas Aktif -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-trophy text-warning me-2"></i>
                        Top 10 Pengawas Aktif
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4" width="50">Rank</th>
                                    <th class="py-3">NIP</th>
                                    <th class="py-3">Nama Pengawas</th>
                                    <th class="py-3 text-center pe-4">Total Kinerja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; foreach ($topPengawas as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if ($rank == 1): ?>
                                            <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-crown me-1"></i><?php echo $rank; ?></span>
                                        <?php elseif ($rank == 2): ?>
                                            <span class="badge bg-secondary px-2 py-1"><i class="fas fa-medal me-1"></i><?php echo $rank; ?></span>
                                        <?php elseif ($rank == 3): ?>
                                            <span class="badge bg-danger px-2 py-1"><i class="fas fa-award me-1"></i><?php echo $rank; ?></span>
                                        <?php else: ?>
                                            <span class="text-muted fw-bold"><?php echo $rank; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?php echo $p['nip']; ?></td>
                                    <td class="fw-semibold"><?php echo $p['nama_lengkap']; ?></td>
                                    <td class="text-center pe-4">
                                        <span class="badge bg-primary px-3 py-2"><?php echo $p['total_kinerja']; ?></span>
                                    </td>
                                </tr>
                                <?php $rank++; endforeach; ?>
                                
                                <?php if (empty($topPengawas)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Belum ada data kinerja
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Initialize Charts -->
    <script src="<?php echo base_url('assets/js/dashboard-charts.js'); ?>"></script>
    <script>
        // Data from PHP
        const monthlyData = {
            labels: <?php echo json_encode($monthlyData['labels']); ?>,
            values: <?php echo json_encode($monthlyData['values']); ?>
        };
        
        const jenjangData = {
            labels: <?php echo json_encode($jenjangData['labels']); ?>,
            values: <?php echo json_encode($jenjangData['values']); ?>
        };
        
        const kecamatanData = {
            labels: <?php echo json_encode($kecamatanData['labels']); ?>,
            values: <?php echo json_encode($kecamatanData['values']); ?>
        };
        
        // Initialize all charts
        initAllCharts(monthlyData, jenjangData, kecamatanData);
    </script>
    
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
