<?php
// modules/admin/pengawasan/index.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('admin');

// Query untuk mendapatkan semua assignment
$query = "SELECT 
    pm.id,
    pm.pengawas_id,
    pm.madrasah_id,
    u.nama_lengkap as pengawas,
    u.nip,
    m.nama_madrasah,
    m.nsm,
    pm.tanggal_penugasan,
    pm.status,
    pm.keterangan
FROM pengawas_madrasah pm
INNER JOIN users u ON pm.pengawas_id = u.id
INNER JOIN madrasah m ON pm.madrasah_id = m.id
ORDER BY u.nama_lengkap, m.nama_madrasah";

$result = mysqli_query($conn, $query);

// Group by pengawas untuk tampilan yang lebih rapi
$assignments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pengawas_id = $row['pengawas_id'];
    if (!isset($assignments[$pengawas_id])) {
        $assignments[$pengawas_id] = [
            'pengawas' => $row['pengawas'],
            'nip' => $row['nip'],
            'madrasah' => []
        ];
    }
    $assignments[$pengawas_id]['madrasah'][] = $row;
}
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">
            <i class="fas fa-users-cog me-2 text-primary"></i>
            Kelola Pengawasan Madrasah
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="tambah.php" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Assignment
            </a>
        </div>
    </div>

    <?php echo flash(); ?>

    <div class="row">
        <div class="col-12">
            <?php if (empty($assignments)): ?>
                <div class="alert alert-info border-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Belum ada assignment pengawas. Silakan tambah assignment baru.
                </div>
            <?php else: ?>
                <div class="accordion" id="accordionPengawasan">
                    <?php 
                    $accordion_index = 0;
                    foreach ($assignments as $pengawas_id => $data): 
                        $accordion_index++;
                        $collapse_id = "collapse" . $pengawas_id;
                    ?>
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-0 p-0" id="heading<?php echo $pengawas_id; ?>">
                                <h2 class="accordion-header mb-0">
                                    <button class="accordion-button <?php echo $accordion_index == 1 ? '' : 'collapsed'; ?>" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#<?php echo $collapse_id; ?>" 
                                            aria-expanded="<?php echo $accordion_index == 1 ? 'true' : 'false'; ?>" 
                                            aria-controls="<?php echo $collapse_id; ?>">
                                        <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                            <div>
                                                <i class="fas fa-user-tie me-2 text-primary"></i>
                                                <strong><?php echo htmlspecialchars($data['pengawas']); ?></strong>
                                                <small class="text-muted ms-2">(NIP: <?php echo htmlspecialchars($data['nip']); ?>)</small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">
                                                <?php echo count($data['madrasah']); ?> Madrasah
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                            </div>

                            <div id="<?php echo $collapse_id; ?>" 
                                 class="collapse <?php echo $accordion_index == 1 ? 'show' : ''; ?>" 
                                 aria-labelledby="heading<?php echo $pengawas_id; ?>" 
                                 data-bs-parent="#accordionPengawasan">
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($data['madrasah'] as $m): ?>
                                            <div class="list-group-item px-0 py-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 fw-bold">
                                                            <i class="fas fa-school me-2 text-success"></i>
                                                            <?php echo htmlspecialchars($m['nama_madrasah']); ?>
                                                        </h6>
                                                        <div class="small text-muted">
                                                            <span class="me-3">
                                                                <i class="fas fa-id-card me-1"></i>
                                                                NSM: <strong><?php echo htmlspecialchars($m['nsm']); ?></strong>
                                                            </span>
                                                            <span class="me-3">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                Sejak: <?php echo date('d M Y', strtotime($m['tanggal_penugasan'])); ?>
                                                            </span>
                                                            <?php if ($m['status'] == 'aktif'): ?>
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">
                                                                    <i class="fas fa-times-circle me-1"></i>Nonaktif
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ($m['keterangan']): ?>
                                                            <div class="small text-muted mt-2">
                                                                <i class="fas fa-sticky-note me-1"></i>
                                                                <?php echo htmlspecialchars($m['keterangan']); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="btn-group btn-group-sm ms-3" role="group">
                                                        <a href="edit.php?id=<?php echo $m['id']; ?>" 
                                                           class="btn btn-outline-warning" 
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="aksi.php?act=delete&id=<?php echo $m['id']; ?>" 
                                                           class="btn btn-outline-danger"
                                                           onclick="return confirm('Yakin hapus assignment ini?')"
                                                           title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
