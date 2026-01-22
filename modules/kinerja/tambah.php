<?php
// modules/kinerja/tambah.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

checkRole('pengawas');
$kegiatan = getAllKegiatan($conn);
// Hanya tampilkan madrasah yang di-assign ke pengawas ini
$madrasahs = getMadrasahByPengawas($conn, $_SESSION['user_id']);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Tambah Kinerja</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

    <?php echo flash(); ?>

    <!-- Alert Container - Better Desktop Layout -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10 col-xl-9">
            <!-- GPS Alert -->
            <div class="alert alert-warning border-0 shadow-sm mb-3" id="gpsAlert" style="display:none;">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-triangle fa-2x me-3 mt-1 text-warning"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-2 fw-bold">⚠️ GPS Tidak Aktif atau Tidak Diizinkan</h6>
                        <p class="mb-0 small">Untuk validasi yang lebih baik, pastikan GPS HP Anda aktif saat mengambil foto bukti. Foto dengan GPS akan lebih mudah diverifikasi oleh pimpinan.</p>
                    </div>
                </div>
            </div>

            <!-- Tips Upload -->
            <div class="alert alert-info border-0 shadow-sm mb-0">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle fa-2x me-3 mt-1 text-info"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-2 fw-bold">💡 Tips Upload Bukti</h6>
                        <ul class="mb-0 small ps-3">
                            <li>Aktifkan GPS/Lokasi di HP sebelum foto</li>
                            <li>Ambil foto langsung dari kamera (jangan screenshot)</li>
                            <li>Upload segera setelah kegiatan</li>
                            <li>Foto akan otomatis terverifikasi waktu & lokasinya</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="aksi.php?act=insert" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="tanggal_kegiatan" class="form-label fw-bold">Tanggal Kegiatan</label>
                            <input type="date" class="form-control bg-light border-0" id="tanggal_kegiatan" name="tanggal_kegiatan" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="madrasah_id" class="form-label fw-bold">Lokasi Madrasah Binaan <span class="text-muted fw-normal">(Opsional)</span></label>
                            <select class="form-select bg-light border-0" id="madrasah_id" name="madrasah_id">
                                <option value="">-- Pilih Madrasah --</option>
                                <?php foreach($madrasahs as $m): ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo $m['nama_madrasah']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Kosongkan jika kegiatan dilakukan di kantor atau WFH.</div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-bold">Deskripsi Rinci Kegiatan</label>
                            <textarea class="form-control bg-light border-0" id="deskripsi" name="deskripsi" rows="4" required placeholder="Jelaskan detail apa yang dilakukan..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="file_bukti" class="form-label fw-bold">Bukti Fisik <span class="text-muted fw-normal">(Foto/Dokumen PDF)</span></label>
                            <input class="form-control bg-light border-0" type="file" id="file_bukti" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">Format: JPG, PNG, PDF. Maksimal 2MB.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Check GPS/Geolocation status saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    // Check GPS segera saat halaman load
    setTimeout(function() {
        checkGPSStatusProactive();
    }, 1000);
    
    // Check lagi saat user klik file input
    document.getElementById('file_bukti').addEventListener('click', function(e) {
        checkGPSBeforeUpload(e);
    });
});

function checkGPSStatusProactive() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // GPS aktif dan diizinkan
                document.getElementById('gpsAlert').style.display = 'none';
                console.log('✅ GPS Active:', position.coords.latitude, position.coords.longitude);
            },
            function(error) {
                // GPS tidak aktif atau tidak diizinkan - LANGSUNG TANYA USER
                showGPSConfirmation(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    } else {
        // Browser tidak support geolocation
        alert('⚠️ Browser Anda tidak mendukung GPS. Gunakan browser modern seperti Chrome atau Firefox.');
    }
}

function checkGPSBeforeUpload(event) {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // GPS OK, lanjutkan
                document.getElementById('gpsAlert').style.display = 'none';
            },
            function(error) {
                // GPS mati, tanya user
                event.preventDefault();
                showGPSConfirmation(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 3000,
                maximumAge: 0
            }
        );
    }
}

function showGPSConfirmation(error) {
    document.getElementById('gpsAlert').style.display = 'block';
    
    let message = '';
    let canOpenSettings = false;
    
    if (error.code === 1) { // PERMISSION_DENIED
        message = '📍 GPS tidak diizinkan!\n\n' +
                  'Untuk validasi bukti yang lebih baik, aktifkan izin lokasi:\n\n' +
                  '1. Klik icon gembok/info di address bar\n' +
                  '2. Pilih "Izinkan" untuk Lokasi\n' +
                  '3. Refresh halaman ini\n\n' +
                  'Atau klik OK untuk membuka pengaturan browser.';
        canOpenSettings = true;
    } else if (error.code === 2) { // POSITION_UNAVAILABLE
        message = '📍 GPS tidak tersedia!\n\n' +
                  'Pastikan:\n' +
                  '• GPS/Lokasi HP sudah aktif\n' +
                  '• Anda berada di area dengan sinyal GPS\n' +
                  '• Tidak dalam mode pesawat\n\n' +
                  'Klik OK untuk coba aktifkan GPS.';
        canOpenSettings = true;
    } else if (error.code === 3) { // TIMEOUT
        message = '⏱️ GPS timeout!\n\n' +
                  'Sinyal GPS lemah. Coba:\n' +
                  '• Pindah ke area terbuka\n' +
                  '• Tunggu beberapa saat\n' +
                  '• Refresh halaman';
    }
    
    if (confirm(message)) {
        if (canOpenSettings) {
            // Coba minta izin GPS lagi
            requestGPSPermission();
        }
    } else {
        // User pilih tidak, tampilkan warning permanen
        alert('⚠️ Upload tanpa GPS tetap bisa dilakukan, tapi akan lebih sulit diverifikasi oleh pimpinan.\n\nDisarankan untuk mengaktifkan GPS agar foto Anda otomatis terverifikasi waktu dan lokasinya.');
    }
}

function requestGPSPermission() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                alert('✅ GPS Berhasil Diaktifkan!\n\nLokasi: ' + 
                      position.coords.latitude.toFixed(6) + ', ' + 
                      position.coords.longitude.toFixed(6) + '\n\n' +
                      'Sekarang foto Anda akan otomatis terverifikasi.');
                document.getElementById('gpsAlert').style.display = 'none';
                location.reload(); // Refresh untuk update status
            },
            function(error) {
                alert('❌ GPS masih belum aktif.\n\n' +
                      'Silakan aktifkan GPS di pengaturan HP Anda:\n' +
                      'Settings > Location/Lokasi > ON\n\n' +
                      'Kemudian refresh halaman ini.');
                document.getElementById('gpsAlert').style.display = 'block';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }
}
</script>


<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
