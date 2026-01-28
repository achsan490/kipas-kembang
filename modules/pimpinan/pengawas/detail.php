<?php
// modules/pimpinan/pengawas/detail.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('pimpinan');

// Get pengawas ID
$pengawas_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($pengawas_id == 0) {
    setFlash('error', 'ID Pengawas tidak valid');
    redirect('index.php');
}

// Get pengawas detail
$pengawas = getPengawasDetail($conn, $pengawas_id);

if (!$pengawas) {
    setFlash('error', 'Data pengawas tidak ditemukan');
    redirect('index.php');
}

// Get timeline
$timeline = getPengawasTimeline($conn, $pengawas_id, 5);

// Get performance data for chart
$performance = getPengawasPerformance($conn, $pengawas_id, 6);
?>

<main class="main-content w-100">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">
            <i class="fas fa-user-tie me-2 text-primary"></i>
            Profil Pengawas
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <?php echo flash(); ?>

    <div class="row g-4">
        <!-- Left Column: Profil & Madrasah -->
        <div class="col-lg-4">
            <!-- Card Profil -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="profile-avatar mx-auto mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($pengawas['nama_lengkap']); ?></h4>
                        <p class="text-muted mb-2"><?php echo htmlspecialchars($pengawas['jabatan'] ?? '-'); ?></p>
                        <span class="badge bg-primary px-3 py-2"><?php echo htmlspecialchars($pengawas['nip']); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="text-start">
                        <div class="info-item mb-2">
                            <i class="fas fa-calendar-alt text-muted me-2"></i>
                            <span class="text-muted small">Bergabung: <?php echo date('d M Y', strtotime($pengawas['created_at'])); ?></span>
                        </div>
                        <?php if ($pengawas['last_activity']): ?>
                        <div class="info-item">
                            <i class="fas fa-clock text-muted me-2"></i>
                            <span class="text-muted small">Aktivitas Terakhir: <?php echo date('d M Y', strtotime($pengawas['last_activity'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Card Statistik Kinerja -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m fw-bold">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Statistik Kinerja
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h5 class="mb-1 fw-bold text-primary"><?php echo $pengawas['total_kinerja']; ?></h5>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h5 class="mb-1 fw-bold text-success"><?php echo $pengawas['approved']; ?></h5>
                                <small class="text-muted">Disetujui</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Card Madrasah Binaan -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-school me-2 text-primary"></i>
                        Madrasah Binaan
                        <span class="badge bg-primary ms-2"><?php echo count($pengawas['madrasah_binaan']); ?></span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php if (count($pengawas['madrasah_binaan']) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($pengawas['madrasah_binaan'] as $m): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="me-2">
                                    <h6 class="mb-1 fw-semibold"><?php echo htmlspecialchars($m['nama_madrasah']); ?></h6>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-hashtag me-1"></i><?php echo $m['nsm']; ?>
                                    </p>
                                    <?php if (!empty($m['jenjang'])): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><?php echo $m['jenjang']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-school fa-2x mb-2 d-block"></i>
                        Belum ada madrasah binaan
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Charts & Timeline -->
        <div class="col-lg-8">
            <!-- Grafik Performa 6 Bulan -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Performa 6 Bulan Terakhir
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="80"></canvas>
                </div>
            </div>

            <!-- Timeline Aktivitas -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Timeline Aktivitas Terakhir
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (count($timeline) > 0): ?>
                    <div class="timeline">
                        <?php foreach ($timeline as $t): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker 
                                <?php 
                                    if ($t['status'] == 'disetujui') echo 'bg-success';
                                    elseif ($t['status'] == 'ditolak') echo 'bg-danger';
                                    else echo 'bg-warning';
                                ?>
                            "></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1 fw-semibold"><?php echo htmlspecialchars($t['nama_kegiatan']); ?></h6>
                                    <small class="text-muted"><?php echo date('d M Y', strtotime($t['tanggal_kegiatan'])); ?></small>
                                </div>
                                <?php if ($t['nama_madrasah']): ?>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-school me-1"></i><?php echo htmlspecialchars($t['nama_madrasah']); ?>
                                </p>
                                <?php endif; ?>
                                <p class="mb-2 text-muted small"><?php echo htmlspecialchars($t['deskripsi']); ?></p>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge 
                                        <?php 
                                            if ($t['status'] == 'disetujui') echo 'bg-success';
                                            elseif ($t['status'] == 'ditolak') echo 'bg-danger';
                                            else echo 'bg-warning';
                                        ?>
                                    ">
                                        <?php echo ucfirst($t['status']); ?>
                                    </span>

                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-history fa-3x mb-2 d-block"></i>
                        Belum ada aktivitas
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.profile-avatar {
    width: 120px;
    height: 120px;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 25px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item:not(:last-child):before {
    content: "";
    position: absolute;
    left: -23px;
    top: 20px;
    height: calc(100% + 5px);
    width: 2px;
    background-color: #e0e0e0;
}

.timeline-marker {
    position: absolute;
    left: -28px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px currentColor;
}

.timeline-content {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #0d6efd;
}
</style>

<script>
// Performance Chart Data
const performanceData = {
    labels: <?php echo json_encode($performance['labels']); ?>,
    kinerja: <?php echo json_encode($performance['kinerja']); ?>
};

// Initialize Chart
const ctx = document.getElementById('performanceChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: performanceData.labels,
        datasets: [
            {
                label: 'Jumlah Kinerja',
                data: performanceData.kinerja,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                enabled: true,
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                bodySpacing: 4
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Jumlah Kinerja'
                },
                beginAtZero: true
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
