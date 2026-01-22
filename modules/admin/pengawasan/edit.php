<?php
// modules/admin/pengawasan/edit.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('admin');

$id = intval($_GET['id'] ?? 0);

// Get assignment data
$query = "SELECT 
    pm.*,
    u.nama_lengkap as pengawas,
    u.nip,
    m.nama_madrasah,
    m.nsm
FROM pengawas_madrasah pm
INNER JOIN users u ON pm.pengawas_id = u.id
INNER JOIN madrasah m ON pm.madrasah_id = m.id
WHERE pm.id = $id";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    flash('danger', 'Data tidak ditemukan!');
    redirect('modules/admin/pengawasan/index.php');
}

$data = mysqli_fetch_assoc($result);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">
            <i class="fas fa-edit me-2 text-warning"></i>
            Edit Assignment Pengawasan
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <?php echo flash(); ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <!-- Info Assignment -->
                    <div class="alert alert-info border-0 mb-4">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Assignment
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Pengawas:</strong><br>
                                    <?php echo htmlspecialchars($data['pengawas']); ?>
                                </p>
                                <p class="mb-0">
                                    <strong>NIP:</strong> <?php echo htmlspecialchars($data['nip']); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Madrasah:</strong><br>
                                    <?php echo htmlspecialchars($data['nama_madrasah']); ?>
                                </p>
                                <p class="mb-0">
                                    <strong>NSM:</strong> <?php echo htmlspecialchars($data['nsm']); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="aksi.php?act=update" method="POST">
                        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

                        <div class="mb-4">
                            <label for="status" class="form-label fw-bold">
                                <i class="fas fa-toggle-on me-2 text-primary"></i>
                                Status Assignment
                            </label>
                            <select class="form-select bg-light border-0" id="status" name="status" required>
                                <option value="aktif" <?php echo $data['status'] == 'aktif' ? 'selected' : ''; ?>>
                                    ✅ Aktif
                                </option>
                                <option value="nonaktif" <?php echo $data['status'] == 'nonaktif' ? 'selected' : ''; ?>>
                                    ❌ Nonaktif
                                </option>
                            </select>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Status "Nonaktif" akan menonaktifkan assignment ini tanpa menghapus data
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_penugasan" class="form-label fw-bold">
                                <i class="fas fa-calendar me-2 text-primary"></i>
                                Tanggal Penugasan
                            </label>
                            <input type="date" 
                                   class="form-control bg-light border-0" 
                                   id="tanggal_penugasan" 
                                   name="tanggal_penugasan" 
                                   value="<?php echo $data['tanggal_penugasan']; ?>" 
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold">
                                <i class="fas fa-sticky-note me-2 text-primary"></i>
                                Keterangan <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <textarea class="form-control bg-light border-0" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3" 
                                      placeholder="Catatan tambahan tentang penugasan ini..."><?php echo htmlspecialchars($data['keterangan']); ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning py-3 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Update Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
