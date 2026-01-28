<?php
// modules/kinerja/edit.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

checkRole('pengawas');

$id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Ambil data kinerja yang akan diedit
$query = "SELECT k.*, jk.nama_kegiatan 
          FROM kinerja k 
          JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
          WHERE k.id = $id AND k.user_id = $user_id AND k.status != 'disetujui'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    flash('danger', 'Data tidak ditemukan atau tidak bisa diedit.');
    redirect('modules/kinerja/index.php');
}

$data = mysqli_fetch_assoc($result);
$kegiatan = getAllKegiatan($conn);
// Hanya tampilkan madrasah yang di-assign ke pengawas ini
$madrasahs = getMadrasahByPengawas($conn, $_SESSION['user_id']);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Edit Kinerja</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <?php echo flash(); ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="aksi.php?act=update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="tanggal_kegiatan" class="form-label fw-bold">Tanggal Kegiatan</label>
                            <input type="date" class="form-control bg-light border-0" id="tanggal_kegiatan" name="tanggal_kegiatan" value="<?php echo $data['tanggal_kegiatan']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="madrasah_id" class="form-label fw-bold">Lokasi Madrasah Binaan <span class="text-muted fw-normal">(Opsional)</span></label>
                            <select class="form-select bg-light border-0" id="madrasah_id" name="madrasah_id">
                                <option value="">-- Pilih Madrasah --</option>
                                <?php foreach($madrasahs as $m): ?>
                                    <option value="<?php echo $m['id']; ?>" <?php echo ($m['id'] == $data['madrasah_id']) ? 'selected' : ''; ?>>
                                        <?php echo $m['nama_madrasah']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Kosongkan jika kegiatan dilakukan di kantor atau WFH.</div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-bold">Deskripsi Rinci Kegiatan</label>
                            <textarea class="form-control bg-light border-0" id="deskripsi" name="deskripsi" rows="4" required placeholder="Jelaskan detail apa yang dilakukan..."><?php echo $data['deskripsi']; ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="file_bukti" class="form-label fw-bold">Bukti Fisik <span class="text-muted fw-normal">(Foto/Dokumen PDF)</span></label>
                            
                            <?php if($data['file_bukti']): ?>
                                <div class="alert alert-info mb-2">
                                    <i class="fas fa-file me-2"></i> File saat ini: 
                                    <a href="<?php echo base_url('uploads/'.$data['file_bukti']); ?>" target="_blank" class="alert-link">
                                        <?php echo $data['file_bukti']; ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <input class="form-control bg-light border-0" type="file" id="file_bukti" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">Upload file baru jika ingin mengganti. Format: JPG, PNG, PDF. Maksimal 2MB.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Update Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
