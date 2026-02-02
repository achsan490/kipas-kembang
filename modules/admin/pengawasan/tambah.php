<?php
// modules/admin/pengawasan/tambah.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('admin');

// Get all pengawas
$pengawas_query = "SELECT id, nip, nama_lengkap FROM users WHERE role = 'pengawas' ORDER BY nama_lengkap ASC";
$pengawas_result = mysqli_query($conn, $pengawas_query);

// Get madrasah yang BELUM punya pengawas aktif
// Exclude madrasah yang sudah di-assign ke pengawas lain dengan status aktif
$madrasah_query = "SELECT m.id, m.nsm, m.nama_madrasah 
                   FROM madrasah m
                   LEFT JOIN pengawas_madrasah pm ON m.id = pm.madrasah_id AND pm.status = 'aktif'
                   WHERE pm.id IS NULL
                   ORDER BY m.nama_madrasah ASC";
$madrasah_result = mysqli_query($conn, $madrasah_query);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">
            <i class="fas fa-plus-circle me-2 text-primary"></i>
            Tambah Assignment Pengawasan
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
                    <form action="aksi.php?act=insert" method="POST">
                        <div class="mb-4">
                            <label for="pengawas_id" class="form-label fw-bold">
                                <i class="fas fa-user-tie me-2 text-primary"></i>
                                Pilih Pengawas
                            </label>
                            <select class="form-select bg-light border-0" 
                                    id="pengawas_id" 
                                    name="pengawas_id" 
                                    required
                                    style="width: 100%;">
                                <option value="">-- Pilih Pengawas --</option>
                                <?php while ($p = mysqli_fetch_assoc($pengawas_result)): ?>
                                    <option value="<?php echo $p['id']; ?>">
                                        <?php echo htmlspecialchars($p['nama_lengkap']); ?> 
                                        (NIP: <?php echo htmlspecialchars($p['nip']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Pilih pengawas yang akan ditugaskan. Gunakan pencarian untuk menemukan pengawas dengan cepat.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-school me-2 text-primary"></i>
                                Pilih Madrasah yang Diawasi
                            </label>
                            
                            <?php 
                            $madrasah_count = mysqli_num_rows($madrasah_result);
                            
                            if ($madrasah_count == 0): 
                            ?>
                                <div class="alert alert-warning border-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Semua madrasah sudah memiliki pengawas!</strong><br>
                                    Tidak ada madrasah yang tersedia untuk di-assign. Jika ingin mengubah pengawas suatu madrasah, 
                                    nonaktifkan assignment yang lama terlebih dahulu.
                                </div>
                            <?php else: ?>
                                <select class="form-select bg-light border-0" 
                                        id="madrasah_ids" 
                                        name="madrasah_ids[]" 
                                        multiple 
                                        required
                                        style="width: 100%;">
                                    <?php 
                                    mysqli_data_seek($madrasah_result, 0);
                                    while ($m = mysqli_fetch_assoc($madrasah_result)): 
                                    ?>
                                        <option value="<?php echo $m['id']; ?>">
                                            <?php echo htmlspecialchars($m['nama_madrasah']); ?> 
                                            (NSM: <?php echo htmlspecialchars($m['nsm']); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Catatan:</strong> Hanya madrasah yang belum memiliki pengawas aktif yang ditampilkan. 
                                    Satu madrasah hanya bisa diawasi oleh satu pengawas. Gunakan pencarian untuk menemukan madrasah dengan cepat.
                                </div>
                            <?php endif; ?>
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
                                   value="<?php echo date('Y-m-d'); ?>" 
                                   required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tanggal mulai penugasan pengawasan
                            </div>
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
                                      placeholder="Catatan tambahan tentang penugasan ini..."></textarea>
                        </div>

                        <div class="alert alert-info border-0 mb-4">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Kebijakan Sistem:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>Satu madrasah hanya bisa diawasi oleh <strong>satu pengawas aktif</strong></li>
                                <li>Satu pengawas bisa mengawasi <strong>beberapa madrasah</strong></li>
                                <li>Assignment akan langsung aktif setelah disimpan</li>
                                <li>Madrasah yang sudah punya pengawas tidak akan muncul di daftar</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Include jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Initialize Select2 untuk dropdown madrasah
$(document).ready(function() {
    // Initialize Select2 for pengawas dropdown
    $('#pengawas_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Pengawas --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Pengawas tidak ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });
    
    // Initialize Select2 for madrasah dropdown
    $('#madrasah_ids').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih madrasah (bisa lebih dari satu)',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Madrasah tidak ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });
});

// Validasi minimal 1 madrasah dipilih
document.querySelector('form').addEventListener('submit', function(e) {
    const selectedMadrasah = $('#madrasah_ids').val();
    if (!selectedMadrasah || selectedMadrasah.length === 0) {
        e.preventDefault();
        alert('⚠️ Pilih minimal 1 madrasah untuk ditugaskan!');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
