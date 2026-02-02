<?php
// modules/admin/madrasah/index.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('admin');

// Get filter parameters
$filter_jenjang = isset($_GET['jenjang']) ? $_GET['jenjang'] : null;
$filter_kecamatan = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : null;
$filter_status = isset($_GET['status']) ? $_GET['status'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Get data based on filters
if ($search) {
    $madrasahs = searchMadrasah($conn, $search);
} else {
    $madrasahs = getMadrasahWithFilter($conn, $filter_jenjang, $filter_kecamatan, $filter_status);
}

// Get filter options
$jenjang_list = getAllJenjang($conn);
$kecamatan_list = getAllKecamatan($conn);

// Get statistics
$stats = getMadrasahStats($conn);
?>
<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Data Madrasah</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="tambah.php" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Madrasah
            </a>
        </div>
    </div>

    <?php echo flash(); ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title opacity-75">Total Madrasah</h6>
                    <h2 class="mb-0 fw-bold"><?php echo $stats['total']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title opacity-75">Jenjang</h6>
                    <h2 class="mb-0 fw-bold"><?php echo count($stats['per_jenjang']); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title opacity-75">Kecamatan</h6>
                    <h2 class="mb-0 fw-bold"><?php echo count($kecamatan_list); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title opacity-75">Hasil Filter</h6>
                    <h2 class="mb-0 fw-bold"><?php echo count($madrasahs); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pencarian</label>
                    <input type="text" class="form-control" name="search" placeholder="Nama, NSM, NPSN..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Jenjang</label>
                    <select class="form-select" name="jenjang">
                        <option value="">-- Semua Jenjang --</option>
                        <?php foreach($jenjang_list as $j): ?>
                            <option value="<?php echo $j; ?>" <?php echo ($filter_jenjang == $j) ? 'selected' : ''; ?>>
                                <?php echo $j; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Kecamatan</label>
                    <select class="form-select" name="kecamatan">
                        <option value="">-- Semua Kecamatan --</option>
                        <?php foreach($kecamatan_list as $k): ?>
                            <option value="<?php echo $k; ?>" <?php echo ($filter_kecamatan == $k) ? 'selected' : ''; ?>>
                                <?php echo $k; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="negeri" <?php echo ($filter_status == 'negeri') ? 'selected' : ''; ?>>Negeri</option>
                        <option value="swasta" <?php echo ($filter_status == 'swasta') ? 'selected' : ''; ?>>Swasta</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive table-responsive-card">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4">No</th>
                            <th class="py-3">NSM</th>
                            <th class="py-3">NPSN</th>
                            <th class="py-3">Nama Madrasah</th>
                            <th class="py-3">Jenjang</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Kecamatan</th>
                            <th class="py-3">Alamat</th>
                            <th class="py-3 pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($madrasahs) > 0): ?>
                            <?php $no=1; foreach($madrasahs as $row): ?>
                            <tr>
                                <td class="ps-4" data-label="No"><?php echo $no++; ?></td>
                                <td data-label="NSM" class="fw-semibold text-primary"><?php echo $row['nsm']; ?></td>
                                <td data-label="NPSN" class="text-muted"><?php echo $row['npsn'] ?? '-'; ?></td>
                                <td data-label="Nama Madrasah" class="fw-bold text-dark"><?php echo $row['nama_madrasah']; ?></td>
                                <td data-label="Jenjang">
                                    <span class="badge bg-info"><?php echo $row['jenjang'] ?? '-'; ?></span>
                                </td>
                                <td data-label="Status">
                                    <span class="badge <?php echo ($row['status'] == 'negeri') ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo ucfirst($row['status'] ?? '-'); ?>
                                    </span>
                                </td>
                                <td data-label="Kecamatan" class="text-muted"><?php echo $row['kecamatan'] ?? '-'; ?></td>
                                <td data-label="Alamat" class="text-muted small"><?php echo $row['alamat'] ? (strlen($row['alamat']) > 50 ? substr($row['alamat'], 0, 50) . '...' : $row['alamat']) : '-'; ?></td>
                                <td data-label="Aksi" class="pe-4 text-end">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning rounded-circle me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmDelete('aksi.php?act=delete&id=<?php echo $row['id']; ?>')" class="btn btn-sm btn-danger rounded-circle" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada data madrasah
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
