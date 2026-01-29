<?php
// modules/kinerja/index.php - MERGED VERSION (Data Kinerja + Laporan)
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

// Pastikan yang akses adalah pengawas, pendamping, pimpinan, atau admin
checkLogin();
$allowed_roles = ['pengawas', 'pendamping', 'pimpinan', 'admin'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    echo "Akses Ditolak!"; exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get filter values
$filters = [
    'tanggal_mulai' => $_GET['tanggal_mulai'] ?? '',
    'tanggal_selesai' => $_GET['tanggal_selesai'] ?? '',
    'pengawas_id' => ($role === 'pengawas' || $role === 'pendamping') ? $user_id : ($_GET['pengawas_id'] ?? ''),
    'madrasah_id' => $_GET['madrasah_id'] ?? '',
    'jenjang' => $_GET['jenjang'] ?? '',
    'search' => $_GET['search'] ?? ''
];

// Build query with filters
$where_clauses = [];
$params = [];

// Filter by user (pengawas only sees their own data)
if ($role === 'pengawas' || $role === 'pendamping') {
    $where_clauses[] = "k.user_id = " . intval($user_id);
} elseif (!empty($filters['pengawas_id'])) {
    $where_clauses[] = "k.user_id = " . intval($filters['pengawas_id']);
}

// Filter by date range
if (!empty($filters['tanggal_mulai'])) {
    $where_clauses[] = "k.tanggal_kegiatan >= '" . mysqli_real_escape_string($conn, $filters['tanggal_mulai']) . "'";
}
if (!empty($filters['tanggal_selesai'])) {
    $where_clauses[] = "k.tanggal_kegiatan <= '" . mysqli_real_escape_string($conn, $filters['tanggal_selesai']) . "'";
}

// Filter by madrasah
if (!empty($filters['madrasah_id'])) {
    $where_clauses[] = "k.madrasah_id = " . intval($filters['madrasah_id']);
}

// Filter by jenjang
if (!empty($filters['jenjang'])) {
    $where_clauses[] = "m.jenjang = '" . mysqli_real_escape_string($conn, $filters['jenjang']) . "'";
}

// Search in description
if (!empty($filters['search'])) {
    $search = mysqli_real_escape_string($conn, $filters['search']);
    $where_clauses[] = "(k.deskripsi LIKE '%$search%')";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

$query = "SELECT k.*, m.nama_madrasah, m.jenjang, m.kecamatan,
          u.nama_lengkap as pengawas_nama, u.nip
          FROM kinerja k 
          LEFT JOIN madrasah m ON k.madrasah_id = m.id
          LEFT JOIN users u ON k.user_id = u.id
          $where_sql
          ORDER BY k.tanggal_kegiatan DESC, k.created_at DESC";
$result = mysqli_query($conn, $query);

// Get Statistics (Total Only)
$stats_query = "SELECT COUNT(*) as total FROM kinerja k LEFT JOIN madrasah m ON k.madrasah_id = m.id $where_sql";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Get dropdown lists
$madrasahList = getAllMadrasah($conn);
$jenjangList = getAllJenjang($conn);
if ($role === 'pimpinan' || $role === 'admin') {
    $pengawasList = getAllPengawas($conn);
}

// Periode string for print
$periode_str = 'Semua Periode';
if ($filters['tanggal_mulai'] && $filters['tanggal_selesai']) {
    $periode_str = date('d-m-Y', strtotime($filters['tanggal_mulai'])) . ' s/d ' . date('d-m-Y', strtotime($filters['tanggal_selesai']));
}
?>

<main class="main-content w-100">
    <!-- Header (Screen Only) -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom d-print-none">
        <h1 class="h2 fw-bold text-dark">
            <i class="fas fa-clipboard-list me-2 text-primary"></i>
            Kinerja Pengawas
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <?php if ($role === 'pengawas' || $role === 'pendamping'): ?>
                <a href="tambah.php" class="btn btn-primary rounded-pill shadow-sm">
                    <i class="fas fa-plus me-2"></i> Tambah Kinerja
                </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print me-2"></i> Cetak PDF
            </button>
            <a href="export_excel.php?<?php echo http_build_query($filters); ?>" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i> Export Excel
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
                Filter Data
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

                <!-- Pengawas (only for pimpinan/admin) -->
                <?php if ($role === 'pimpinan' || $role === 'admin'): ?>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pengawas</label>
                    <select name="pengawas_id" class="form-select">
                        <option value="">-- Semua Pengawas --</option>
                        <?php foreach ($pengawasList as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo ($filters['pengawas_id'] == $p['id']) ? 'selected' : ''; ?>>
                            <?php echo $p['nama_lengkap']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
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



                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari deskripsi..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>

                <!-- Buttons -->
                <div class="col-md-3">
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

    <!-- Statistics Summary (Screen Only) -->
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-md-3">
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
                            <h3 class="mb-0 fw-bold"><?php echo $stats['total']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive table-responsive-card">
                <table class="table table-hover mb-0 align-middle table-bordered-print">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4">No</th>
                            <?php if ($role === 'pimpinan' || $role === 'admin'): ?>
                            <th class="py-3">Pengawas</th>
                            <?php endif; ?>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Lokasi</th>
                            <th class="py-3">Deskripsi</th>
                            <th class="py-3">Bukti</th>

                            <?php if ($role === 'pengawas' || $role === 'pendamping'): ?>
                            <th class="py-3 pe-4 text-end d-print-none">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        if (mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td class="ps-4" data-label="No"><?php echo $no++; ?></td>
                            <?php if ($role === 'pimpinan' || $role === 'admin'): ?>
                            <td data-label="Pengawas">
                                <div class="fw-semibold"><?php echo $row['pengawas_nama']; ?></div>
                                <small class="text-muted"><?php echo $row['nip']; ?></small>
                            </td>
                            <?php endif; ?>
                            <td data-label="Tanggal"><?php echo date('d-m-Y', strtotime($row['tanggal_kegiatan'])); ?></td>
                            <td data-label="Lokasi">
                                <?php echo $row['nama_madrasah'] ? $row['nama_madrasah'] : '-'; ?>
                                <?php if ($row['jenjang']): ?>
                                    <br><small class="text-muted"><?php echo $row['jenjang']; ?></small>
                                <?php endif; ?>
                            </td>
                            <td data-label="Deskripsi" class="text-muted small"><?php echo substr($row['deskripsi'], 0, 50) . '...'; ?></td>
                            <td data-label="Bukti">
                                <?php if($row['file_bukti']): ?>
                                    <?php 
                                    $file_ext = strtolower(pathinfo($row['file_bukti'], PATHINFO_EXTENSION));
                                    $is_image = in_array($file_ext, ['jpg', 'jpeg', 'png']);
                                    ?>
                                    
                                    <?php if ($is_image): ?>
                                        <button type="button" class="badge bg-primary text-decoration-none rounded-pill px-3 py-2 border-0" 
                                                onclick="showPhotoModal('<?php echo base_url('uploads/'.$row['file_bukti']); ?>', 
                                                                        '<?php echo $row['foto_timestamp'] ? date('d-m-Y H:i:s', strtotime($row['foto_timestamp'])) : ''; ?>', 
                                                                        '<?php echo $row['foto_gps_lat']; ?>', 
                                                                        '<?php echo $row['foto_gps_lng']; ?>')">
                                            <i class="fas fa-eye me-1"></i> Lihat Foto
                                        </button>
                                    <?php else: ?>
                                        <a href="<?php echo base_url('uploads/'.$row['file_bukti']); ?>" target="_blank" class="badge bg-primary text-decoration-none rounded-pill px-3 py-2">
                                            <i class="fas fa-file-pdf me-1"></i> Lihat PDF
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Tampilkan metadata foto jika ada
                                    if ($row['foto_timestamp'] || $row['foto_gps_lat']): 
                                        require_once __DIR__ . '/../../core/exif_helper.php';
                                    ?>
                                        <div class="mt-2">
                                            <?php if ($row['foto_timestamp']): ?>
                                                <div class="badge bg-success-subtle text-success border border-success mb-1" style="font-size: 11px;">
                                                    <i class="fas fa-clock me-1"></i> 
                                                    <?php echo date('d-m-Y H:i:s', strtotime($row['foto_timestamp'])); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($row['foto_gps_lat'] && $row['foto_gps_lng']): ?>
                                                <div>
                                                    <a href="<?php echo getGoogleMapsLink($row['foto_gps_lat'], $row['foto_gps_lng']); ?>" target="_blank" class="badge bg-info-subtle text-info border border-info text-decoration-none" style="font-size: 11px;">
                                                        <i class="fas fa-map-marker-alt me-1"></i> 
                                                        <?php echo number_format($row['foto_gps_lat'], 6) . ', ' . number_format($row['foto_gps_lng'], 6); ?>
                                                        <i class="fas fa-external-link-alt ms-1"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill">Tanpa Bukti</span>
                                <?php endif; ?>
                            </td>

                            <?php if ($role === 'pengawas' || $role === 'pendamping'): ?>
                            <td data-label="Aksi" class="pe-4 text-end d-print-none">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning rounded-circle" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="#" onclick="confirmDelete('<?php echo base_url('modules/kinerja/aksi.php?act=delete&id='.$row['id']); ?>')" class="btn btn-sm btn-danger rounded-circle" title="Hapus"><i class="fas fa-trash"></i></a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($role === 'pimpinan' || $role === 'admin') ? '7' : '6'; ?>" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada data kinerja.
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

@media print {
    .sidebar, .navbar, .d-print-none, .btn, form, footer {
        display: none !important;
    }
    
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    
    body {
        background: white !important;
        font-size: 11px;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table-bordered-print {
        border: 1px solid black !important;
    }
    
    .table-bordered-print th, 
    .table-bordered-print td {
        border: 1px solid black !important;
        padding: 4px 8px !important;
    }
    
    .badge {
        border: 1px solid #000;
        color: #000 !important;
        background: none !important;
        padding: 2px 4px;
    }
    
    tr {
        page-break-inside: avoid;
    }
}
</style>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-image me-2"></i> Bukti Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img id="modalPhoto" src="" class="img-fluid rounded mb-3" style="width: 100%; max-height: 500px; object-fit: contain; background: #f8f9fa;">
                
                <div id="modalMetadata" class="alert alert-info" style="display:none;">
                    <h6 class="fw-bold mb-2"><i class="fas fa-info-circle"></i> Informasi Foto</h6>
                    <div class="row small">
                        <div class="col-md-6" id="modalTimestampContainer" style="display:none;">
                            <strong>📅 Waktu Pengambilan:</strong><br>
                            <span id="modalTimestamp" class="text-muted"></span>
                        </div>
                        <div class="col-md-6" id="modalGPSContainer" style="display:none;">
                            <strong>📍 Lokasi GPS:</strong><br>
                            <a id="modalGPSLink" href="#" target="_blank" class="text-decoration-none">
                                <span id="modalGPS" class="text-primary"></span>
                                <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = url;
    }
}

function showPhotoModal(photoUrl, timestamp, gpsLat, gpsLng) {
    // Set photo
    document.getElementById('modalPhoto').src = photoUrl;
    
    // Show/hide metadata
    const hasMetadata = timestamp || (gpsLat && gpsLng);
    document.getElementById('modalMetadata').style.display = hasMetadata ? 'block' : 'none';
    
    // Set timestamp
    if (timestamp) {
        document.getElementById('modalTimestamp').textContent = timestamp;
        document.getElementById('modalTimestampContainer').style.display = 'block';
    } else {
        document.getElementById('modalTimestampContainer').style.display = 'none';
    }
    
    // Set GPS
    if (gpsLat && gpsLng) {
        document.getElementById('modalGPS').textContent = parseFloat(gpsLat).toFixed(6) + ', ' + parseFloat(gpsLng).toFixed(6);
        document.getElementById('modalGPSLink').href = 'https://www.google.com/maps?q=' + gpsLat + ',' + gpsLng;
        document.getElementById('modalGPSContainer').style.display = 'block';
    } else {
        document.getElementById('modalGPSContainer').style.display = 'none';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('photoModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
