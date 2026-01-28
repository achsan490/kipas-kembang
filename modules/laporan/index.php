<?php
// modules/laporan/index.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

// Pastikan role yang diizinkan: pimpinan, admin, pengawas, pendamping
if ($role !== 'pimpinan' && $role !== 'admin' && $role !== 'pengawas' && $role !== 'pendamping') {
    echo "Akses Ditolak! <a href='" . base_url('modules/dashboard/index.php') . "'>Kembali ke Dashboard</a>";
    exit;
}

// Get filter values
$filters = [
    'tanggal_mulai' => $_GET['tanggal_mulai'] ?? '',
    'tanggal_selesai' => $_GET['tanggal_selesai'] ?? '',
    'pengawas_id' => ($role === 'pengawas' || $role === 'pendamping') ? $_SESSION['user_id'] : ($_GET['pengawas_id'] ?? ''),
    'madrasah_id' => $_GET['madrasah_id'] ?? '',
    'jenjang' => $_GET['jenjang'] ?? '',
    'kecamatan' => $_GET['kecamatan'] ?? '',
    'status' => $_GET['status'] ?? 'disetujui', // Default hanya yang disetujui
    'search_pengawas' => $_GET['search_pengawas'] ?? ''
];

// Get laporan data
$laporan = getLaporanWithFilter($conn, $filters);

// Get list for dropdowns
$pengawasList = getAllPengawas($conn);
$madrasahList = getAllMadrasah($conn);
$jenjangList = getAllJenjang($conn);
$kecamatanList = getAllKecamatan($conn);

// Periode string for print header
$periode_str = 'Semua Periode';
if ($filters['tanggal_mulai'] && $filters['tanggal_selesai']) {
    $periode_str = date('d-m-Y', strtotime($filters['tanggal_mulai'])) . ' s/d ' . date('d-m-Y', strtotime($filters['tanggal_selesai']));
}
?>

<main class="main-content w-100">
    <!-- Header (Screen Only) -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom d-print-none">
        <h1 class="h2 fw-bold text-dark">
            <i class="fas fa-file-alt me-2 text-primary"></i>
            Laporan Kinerja
        </h1>
         <div class="btn-toolbar mb-2 mb-md-0 gap-2">
             <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i> Cetak PDF
            </button>
            <a href="export_excel.php?<?php echo http_build_query($filters); ?>" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i> Ekspor Excel
            </a>
        </div>
    </div>

    <!-- Print Header (Print Only) -->
    <div class="d-none d-print-block text-center mb-4">
        <h3 class="fw-bold mb-1">KEMENTERIAN AGAMA REPUBLIK INDONESIA</h3>
        <h4 class="fw-bold mb-1">KANTOR KEMENTERIAN AGAMA KABUPATEN JOMBANG</h4>
        <h5 class="fw-bold fs-5 mb-3">LAPORAN KINERJA PENGAWAS</h5>
        <div class="border-bottom border-dark pb-2 mb-2"></div>
         <p class="mb-0">Periode: <?php echo $periode_str; ?></p>
    </div>

    <?php echo flash(); ?>

    <!-- Filter Panel (Screen Only) -->
    <div class="card border-0 shadow-sm mb-4 d-print-none">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-filter me-2"></i>
                Filter Laporan
            </h6>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="" class="row g-3">
                <!-- Periode -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="<?php echo $filters['tanggal_mulai']; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="<?php echo $filters['tanggal_selesai']; ?>">
                </div>

                <!-- Pengawas -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pengawas</label>
                    <?php if ($role === 'pengawas'): ?>
                        <input type="text" class="form-control" value="<?php echo $_SESSION['nama']; ?>" readonly>
                        <input type="hidden" name="pengawas_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <?php else: ?>
                    <select name="pengawas_id" class="form-select">
                        <option value="">-- Semua Pengawas --</option>
                        <?php foreach ($pengawasList as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo ($filters['pengawas_id'] == $p['id']) ? 'selected' : ''; ?>>
                            <?php echo $p['nama_lengkap']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </div>

                <!-- Search Nama/NIP Pengawas -->
                <?php if ($role !== 'pengawas'): ?>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Cari Nama/NIP Pengawas</label>
                    <input type="text" name="search_pengawas" class="form-control" placeholder="Ketik nama atau NIP..." list="list-pengawas" value="<?php echo htmlspecialchars($filters['search_pengawas']); ?>" autocomplete="off">
                    <datalist id="list-pengawas">
                        <?php foreach ($pengawasList as $p): ?>
                        <option value="<?php echo htmlspecialchars($p['nama_lengkap']); ?>"><?php echo htmlspecialchars($p['nip']); ?></option>
                        <option value="<?php echo htmlspecialchars($p['nip']); ?>"><?php echo htmlspecialchars($p['nama_lengkap']); ?></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <?php endif; ?>

                <!-- Madrasah -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Madrasah</label>
                    <select name="madrasah_id" class="form-select">
                        <option value="">-- Semua Madrasah --</option>
                        <?php foreach ($madrasahList as $m): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo ($filters['madrasah_id'] == $m['id']) ? 'selected' : ''; ?>>
                            <?php echo $m['nama_madrasah']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Jenjang -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Jenjang</label>
                    <select name="jenjang" class="form-select">
                        <option value="">-- Semua --</option>
                        <?php foreach ($jenjangList as $j): ?>
                        <option value="<?php echo $j; ?>" <?php echo ($filters['jenjang'] == $j) ? 'selected' : ''; ?>>
                            <?php echo $j; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Kecamatan -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Kecamatan</label>
                    <select name="kecamatan" class="form-select">
                        <option value="">-- Semua --</option>
                        <?php foreach ($kecamatanList as $k): ?>
                        <option value="<?php echo $k; ?>" <?php echo ($filters['kecamatan'] == $k) ? 'selected' : ''; ?>>
                            <?php echo $k; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="pending" <?php echo ($filters['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="disetujui" <?php echo ($filters['status'] == 'disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                        <option value="ditolak" <?php echo ($filters['status'] == 'ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-5">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-search me-2"></i> Tampilkan
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4">
                            <i class="fas fa-redo me-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-clipboard-list fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Kinerja</h6>
                            <h3 class="mb-0 fw-bold"><?php echo $laporan['total_kinerja']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light py-3 d-print-none">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-table me-2 text-primary"></i>
                Preview Data Laporan
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle table-bordered-print">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4 text-center" width="5%">No</th>
                            <th class="py-3" width="15%">NIP</th>
                            <th class="py-3" width="20%">Nama Pengawas</th>
                            <th class="py-3 text-center" width="10%">Tanggal</th>
                            <th class="py-3" width="20%">Madrasah</th>
                            <th class="py-3 text-center" width="10%">Jenjang</th>
                            <th class="py-3" width="30%">Kegiatan</th>
                            <th class="py-3 text-center d-print-none">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($laporan['data']) > 0): ?>
                            <?php $no = 1; foreach ($laporan['data'] as $item): ?>
                            <tr>
                                <td class="ps-4 text-center"><?php echo $no++; ?></td>
                                <td><span class="text-muted small"><?php echo $item['nip']; ?></span></td>
                                <td class="fw-semibold"><?php echo $item['pengawas_nama']; ?></td>
                                <td class="text-center small"><?php echo date('d-m-Y', strtotime($item['tanggal_kegiatan'])); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($item['nama_madrasah'] ?? '-'); ?></div>
                                    <?php if ($item['kecamatan']): ?>
                                    <small class="text-muted d-print-none"><?php echo $item['kecamatan']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['jenjang']): ?>
                                    <span class="badge bg-primary d-print-none"><?php echo $item['jenjang']; ?></span>
                                    <span class="d-none d-print-inline"><?php echo $item['jenjang']; ?></span>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo $item['nama_kegiatan']; ?></strong>
                                    <?php if ($item['deskripsi']): ?>
                                    <div class="small text-muted text-truncate d-print-block-full" style="max-width: 250px;">
                                        <?php echo htmlspecialchars($item['deskripsi']); ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center d-print-none">
                                    <?php if ($item['status'] == 'disetujui'): ?>
                                    <span class="badge bg-success">Disetujui</span>
                                    <?php elseif ($item['status'] == 'ditolak'): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            

                            
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block d-print-none"></i>
                                    Tidak ada data untuk filter yang dipilih
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
             <!-- Signature Section (Print Only) -->
            <div class="d-none d-print-block mt-5">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">Jombang, <?php echo date('d F Y'); ?></p>
                        <br><br>
                        <?php if ($role === 'pengawas'): ?>
                            <p class="fw-bold mb-0 text-decoration-underline"><?php echo $_SESSION['nama']; ?></p>
                            <p>NIP. <?php echo $_SESSION['nip'] ?? '-'; ?></p>
                        <?php else: ?>
                            <p class="fw-bold mb-0 text-decoration-underline">Dr. Siti Aminah, M.Ag</p>
                            <p>NIP. 197512122000031002</p>
                        <?php endif; ?>
                    </div>
                </div>
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

@media print {
    /* Hide non-printable elements */
    .sidebar, .navbar, .d-print-none, .btn, form, footer {
        display: none !important;
    }
    
    /* Layout Adjustments */
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    
    body {
        background: white !important;
        font-size: 11px; /* Reduce font size for print */
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    /* Table Styling for Print */
    .table-bordered-print {
        border: 1px solid black !important;
    }
    
    .table-bordered-print th, 
    .table-bordered-print td {
        border: 1px solid black !important;
        padding: 4px 8px !important;
    }
    
    .text-truncate {
        white-space: normal !important;
        overflow: visible !important;
        max-width: none !important;
    }
    
    .badge {
        border: 1px solid #000;
        color: #000 !important;
        background: none !important;
        padding: 2px 4px;
    }
    
    .text-dark-print {
        color: black !important;
    }
    
    h1, h2, h3, h4, h5, h6 {
        color: black !important;
    }
    
    /* Page Break */
    tr {
        page-break-inside: avoid;
    }
}
</style>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
