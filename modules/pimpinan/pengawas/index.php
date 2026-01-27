<?php
// modules/pimpinan/pengawas/index.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('pimpinan');

// Get pengawas data with statistics
$pengawasList = getPengawasWithStats($conn);

// Filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

if ($search) {
    $pengawasList = array_filter($pengawasList, function($p) use ($search) {
        return stripos($p['nama_lengkap'], $search) !== false || 
               stripos($p['nip'], $search) !== false;
    });
}

if ($status_filter) {
    $pengawasList = array_filter($pengawasList, function($p) use ($status_filter) {
        if ($status_filter == 'aktif') return $p['is_active'];
        if ($status_filter == 'nonaktif') return !$p['is_active'];
        return true;
    });
}
?>

<main class="main-content w-100">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">
            <i class="fas fa-users-cog me-2 text-primary"></i>
            Monitoring Pengawas
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                <i class="fas fa-users me-2"></i>
                <?php echo count($pengawasList); ?> Pengawas
            </span>
        </div>
    </div>

    <?php echo flash(); ?>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Pengawas</h6>
                            <h3 class="mb-0 fw-bold"><?php echo count($pengawasList); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Aktif (30 Hari)</h6>
                            <h3 class="mb-0 fw-bold">
                                <?php echo count(array_filter($pengawasList, fn($p) => $p['is_active'])); ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Tidak Aktif</h6>
                            <h3 class="mb-0 fw-bold">
                                <?php echo count(array_filter($pengawasList, fn($p) => !$p['is_active'])); ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Kinerja</h6>
                            <h3 class="mb-0 fw-bold">
                                <?php echo array_sum(array_column($pengawasList, 'total_kinerja')); ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Pencarian</label>
                    <input type="text" class="form-control" name="search" placeholder="Cari nama atau NIP..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="aktif" <?php echo ($status_filter == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="nonaktif" <?php echo ($status_filter == 'nonaktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Pengawas -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4">No</th>
                            <th class="py-3">NIP</th>
                            <th class="py-3">Nama Lengkap</th>
                            <th class="py-3">Jabatan</th>
                            <th class="py-3 text-center">Madrasah Binaan</th>
                            <th class="py-3 text-center">Total Kinerja</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pengawasList) > 0): ?>
                            <?php $no = 1; foreach ($pengawasList as $p): ?>
                            <tr>
                                <td class="ps-4"><?php echo $no++; ?></td>
                                <td>
                                    <span class="text-muted small"><?php echo $p['nip']; ?></span>
                                </td>
                                <td class="fw-semibold"><?php echo $p['nama_lengkap']; ?></td>
                                <td>
                                    <span class="text-muted small"><?php echo $p['jabatan'] ?? '-'; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary px-3 py-2"><?php echo $p['total_madrasah']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info px-3 py-2"><?php echo $p['total_kinerja']; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['is_active']): ?>
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-clock me-1"></i> 
                                            <?php echo isset($p['days_inactive']) ? $p['days_inactive'] . ' hari' : 'Tidak Aktif'; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada data pengawas
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
.stat-icon-box {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
