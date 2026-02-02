<?php
// modules/madrasah/madrasah_saya.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

checkRole('pengawas');

$user_id = $_SESSION['user_id'];

// Query untuk mendapatkan madrasah yang diawasi beserta statistik
$query = "SELECT 
    m.*,
    pm.tanggal_penugasan,
    pm.keterangan,
    COUNT(DISTINCT k.id) as total_kinerja,
    SUM(CASE WHEN k.status = 'disetujui' THEN jk.poin_kredit ELSE 0 END) as total_poin
FROM pengawas_madrasah pm
INNER JOIN madrasah m ON pm.madrasah_id = m.id
LEFT JOIN kinerja k ON k.madrasah_id = m.id AND k.user_id = $user_id
LEFT JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
WHERE pm.pengawas_id = $user_id AND pm.status = 'aktif'
GROUP BY m.id, pm.tanggal_penugasan, pm.keterangan
ORDER BY m.nama_madrasah ASC";

$result = mysqli_query($conn, $query);
$madrasah_count = mysqli_num_rows($result);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">
            <i class="fas fa-school me-2 text-primary"></i>
            Madrasah Binaan Saya
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                <i class="fas fa-building me-2"></i>
                <?php echo $madrasah_count; ?> Madrasah
            </span>
        </div>
    </div>

    <?php echo flash(); ?>

    <?php if ($madrasah_count == 0): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning border-0 shadow-sm">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle fa-3x me-3 text-warning"></i>
                        <div>
                            <h5 class="fw-bold mb-2">Belum Ada Madrasah yang Ditugaskan</h5>
                            <p class="mb-0">
                                Anda belum ditugaskan untuk mengawasi madrasah manapun. 
                                Silakan hubungi admin untuk mendapatkan assignment madrasah binaan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php while ($m = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 hover-shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-school me-2"></i>
                                <?php echo htmlspecialchars($m['nama_madrasah']); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- NSM & NPSN -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-id-card me-1"></i> NSM
                                    </small>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($m['nsm']); ?>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-id-badge me-1"></i> NPSN
                                    </small>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($m['npsn'] ?? '-'); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Jenjang & Status -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-graduation-cap me-1"></i> Jenjang
                                    </small>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($m['jenjang'] ?? '-'); ?>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-tag me-1"></i> Status
                                    </small>
                                    <span class="badge <?php echo ($m['status'] == 'negeri') ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo ucfirst($m['status'] ?? '-'); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Kecamatan -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i> Kecamatan
                                </small>
                                <span class="badge bg-primary">
                                    <?php echo htmlspecialchars($m['kecamatan'] ?? '-'); ?>
                                </span>
                            </div>

                            <!-- Alamat -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-location-dot me-1"></i> Alamat Lengkap
                                </small>
                                <p class="mb-0 small">
                                    <?php echo $m['alamat'] ? htmlspecialchars($m['alamat']) : '-'; ?>
                                </p>
                            </div>

                            <!-- Kepala Madrasah -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-user-tie me-1"></i> Kepala Madrasah
                                </small>
                                <p class="mb-0 small fw-semibold">
                                    <?php echo $m['kepala_madrasah'] ? htmlspecialchars($m['kepala_madrasah']) : '-'; ?>
                                </p>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-calendar-check me-1"></i> Tanggal Penugasan
                                </small>
                                <p class="mb-0 small">
                                    <?php echo date('d F Y', strtotime($m['tanggal_penugasan'])); ?>
                                </p>
                            </div>

                            <?php if ($m['keterangan']): ?>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-sticky-note me-1"></i> Keterangan
                                    </small>
                                    <p class="mb-0 small text-muted">
                                        <?php echo htmlspecialchars($m['keterangan']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <hr>

                            <div class="row text-center">
                                <div class="col-12">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0 fw-bold text-primary">
                                            <?php echo $m['total_kinerja']; ?>
                                        </h4>
                                        <small class="text-muted">Total Laporan Kinerja</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="../kinerja/tambah.php" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-plus me-2"></i>
                                Tambah Kinerja
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</main>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
