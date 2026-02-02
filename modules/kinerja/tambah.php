<?php
// modules/kinerja/tambah.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

checkLogin();
$allowed_roles = ['pengawas', 'pendamping', 'admin'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    echo "Akses Ditolak!"; exit;
}

$kegiatan = getAllKegiatan($conn, $_SESSION['role']);
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
                            <select class="form-select bg-light border-0" id="madrasah_id" name="madrasah_id" style="width: 100%;">
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
                            
                            <!-- Camera Capture Buttons -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="button" class="btn btn-success btn-lg" id="btnCamera" onclick="openCamera()">
                                    <i class="fas fa-camera me-2"></i> 📸 Ambil Foto dengan Kamera
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('file_bukti').click()">
                                    <i class="fas fa-upload me-2"></i> Atau Upload File
                                </button>
                            </div>
                            
                            <!-- Hidden File Input -->
                            <input class="form-control bg-light border-0 d-none" type="file" id="file_bukti" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" onchange="handleFileSelect(event)">
                            
                            <!-- Photo Preview -->
                            <div id="photoPreview" class="card border-0 shadow-sm mb-3" style="display:none;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 fw-bold">Preview Foto</h6>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="clearPhoto()">
                                            <i class="fas fa-times"></i> Hapus
                                        </button>
                                    </div>
                                    <img id="previewImage" src="" class="img-fluid rounded mb-3" style="max-height: 300px; width: 100%; object-fit: contain; background: #f8f9fa;">
                                    
                                    <!-- Metadata Display -->
                                    <div id="metadataDisplay" class="alert alert-info mb-0" style="display:none;">
                                        <h6 class="fw-bold mb-2"><i class="fas fa-info-circle"></i> Informasi Foto</h6>
                                        <div class="row small">
                                            <div class="col-md-6">
                                                <div id="timestampInfo" style="display:none;">
                                                    <strong>📅 Waktu Pengambilan:</strong><br>
                                                    <span id="photoTimestamp" class="text-muted"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="gpsInfo" style="display:none;">
                                                    <strong>📍 Lokasi GPS:</strong><br>
                                                    <a id="gpsLink" href="#" target="_blank" class="text-decoration-none">
                                                        <span id="gpsCoords" class="text-primary"></span>
                                                        <i class="fas fa-external-link-alt ms-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="deviceInfo" class="mt-2 small" style="display:none;">
                                            <strong>📱 Device:</strong> <span id="deviceName" class="text-muted"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Warning if no GPS -->
                                    <div id="noGpsWarning" class="alert alert-warning mb-0" style="display:none;">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <strong>Peringatan:</strong> Foto ini tidak memiliki informasi GPS atau waktu. 
                                        Disarankan mengambil foto langsung dengan kamera HP dengan GPS aktif.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-text">Format: JPG, PNG, PDF. Maksimal 2MB. Gunakan kamera untuk metadata GPS otomatis.</div>
                            
                            <!-- Hidden inputs for metadata -->
                            <input type="hidden" id="metadata_gps_lat" name="metadata_gps_lat">
                            <input type="hidden" id="metadata_gps_lng" name="metadata_gps_lng">
                            <input type="hidden" id="metadata_timestamp" name="metadata_timestamp">
                            <input type="hidden" id="metadata_device" name="metadata_device">
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

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-camera me-2"></i>Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopCamera()"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Live Camera Stream -->
                <div id="cameraContainer" style="position: relative; max-width: 100%; margin: 0 auto;">
                    <video id="cameraStream" autoplay playsinline style="width: 100%; max-height: 60vh; background: #000; border-radius: 8px;"></video>
                    <canvas id="photoCanvas" style="display: none;"></canvas>
                    
                    <!-- Date/Time Overlay -->
                    <div id="dateTimeOverlay" style="position: absolute; bottom: 20px; left: 20px; background: rgba(0,0,0,0.7); color: white; padding: 10px 15px; border-radius: 8px; font-family: monospace; font-size: 14px; display: none;">
                        <div style="font-weight: bold; font-size: 16px;" id="overlayDate">📅 29-01-2026</div>
                        <div style="font-size: 18px; margin-top: 5px;" id="overlayTime">⏰ 10:00:00</div>
                    </div>
                    
                    <!-- GPS Overlay -->
                    <div id="gpsOverlay" style="position: absolute; top: 20px; left: 20px; background: rgba(0,0,0,0.7); color: white; padding: 8px 12px; border-radius: 8px; font-family: monospace; font-size: 12px; display: none;">
                        <div id="overlayGPS">📍 GPS: Loading...</div>
                    </div>
                </div>
                
                <!-- Camera Controls -->
                <div class="mt-3 d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-success btn-lg" id="captureBtn" onclick="capturePhoto()">
                        <i class="fas fa-camera me-2"></i>📸 Ambil Foto
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="stopCamera()" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                </div>
                
                <!-- Loading State -->
                <div id="cameraLoading" style="display: none;" class="mt-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Membuka kamera...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/exif.min.js'); ?>"></script>
<script>
// Global variables
let currentFile = null;
let cameraStream = null;
let videoElement = null;
let clockInterval = null;

// Open camera for photo capture with live stream
async function openCamera() {
    try {
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
        modal.show();
        
        // Show loading
        document.getElementById('cameraLoading').style.display = 'block';
        document.getElementById('cameraContainer').style.display = 'none';
        
        // Get video element
        videoElement = document.getElementById('cameraStream');
        
        // Request camera access with rear camera preference
        const constraints = {
            video: {
                facingMode: 'environment', // Rear camera
                width: { ideal: 1920 },
                height: { ideal: 1080 }
            },
            audio: false
        };
        
        // Get camera stream
        cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        videoElement.srcObject = cameraStream;
        
        // Hide loading, show camera
        document.getElementById('cameraLoading').style.display = 'none';
        document.getElementById('cameraContainer').style.display = 'block';
        
        // Show and start updating date/time overlay
        document.getElementById('dateTimeOverlay').style.display = 'block';
        updateDateTime();
        clockInterval = setInterval(updateDateTime, 1000);
        
        // Get and show GPS overlay
        if (navigator.geolocation) {
            document.getElementById('gpsOverlay').style.display = 'block';
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    document.getElementById('overlayGPS').textContent = `📍 ${lat}, ${lng}`;
                },
                function(error) {
                    document.getElementById('overlayGPS').textContent = '📍 GPS: Tidak tersedia';
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        }
        
    } catch (error) {
        console.error('Camera error:', error);
        
        let errorMessage = '❌ Tidak dapat mengakses kamera!\n\n';
        
        if (error.name === 'NotAllowedError') {
            errorMessage += 'Izin kamera ditolak. Silakan:\n' +
                          '1. Klik icon gembok di address bar\n' +
                          '2. Izinkan akses kamera\n' +
                          '3. Refresh halaman';
        } else if (error.name === 'NotFoundError') {
            errorMessage += 'Kamera tidak ditemukan.\n' +
                          'Pastikan device Anda memiliki kamera.';
        } else {
            errorMessage += 'Error: ' + error.message + '\n\n' +
                          'Gunakan tombol "Upload File" sebagai alternatif.';
        }
        
        alert(errorMessage);
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('cameraModal'))?.hide();
        stopCamera();
    }
}

// Update date/time overlay
function updateDateTime() {
    const now = new Date();
    
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    const dateStr = `📅 ${day}-${month}-${year}`;
    
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const timeStr = `⏰ ${hours}:${minutes}:${seconds}`;
    
    document.getElementById('overlayDate').textContent = dateStr;
    document.getElementById('overlayTime').textContent = timeStr;
}

// Capture photo from video stream
function capturePhoto() {
    if (!videoElement || !cameraStream) {
        alert('❌ Kamera belum siap. Silakan coba lagi.');
        return;
    }
    
    try {
        // Create canvas and capture frame
        const canvas = document.getElementById('photoCanvas');
        const context = canvas.getContext('2d');
        
        // Set canvas size to video size
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        
        // Draw current video frame to canvas
        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
        
        // Convert canvas to blob
        canvas.toBlob(function(blob) {
            if (!blob) {
                alert('❌ Gagal mengambil foto. Silakan coba lagi.');
                return;
            }
            
            // Create file from blob
            const timestamp = new Date().getTime();
            const file = new File([blob], `camera_${timestamp}.jpg`, { 
                type: 'image/jpeg',
                lastModified: timestamp
            });
            
            // Create FileList-like object
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('file_bukti').files = dataTransfer.files;
            
            // Stop camera and close modal
            stopCamera();
            bootstrap.Modal.getInstance(document.getElementById('cameraModal'))?.hide();
            
            // Process the captured photo
            handleFileSelect({ target: { files: [file] } });
            
            // Show success message
            setTimeout(() => {
                alert('✅ Foto berhasil diambil!\n\nSilakan cek preview dan metadata GPS di bawah.');
            }, 300);
            
        }, 'image/jpeg', 0.95); // 95% quality
        
    } catch (error) {
        console.error('Capture error:', error);
        alert('❌ Gagal mengambil foto: ' + error.message);
    }
}

// Stop camera stream
function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    if (videoElement) {
        videoElement.srcObject = null;
    }
    if (clockInterval) {
        clearInterval(clockInterval);
        clockInterval = null;
    }
    // Hide overlays
    document.getElementById('dateTimeOverlay').style.display = 'none';
    document.getElementById('gpsOverlay').style.display = 'none';
}

// Handle file selection (from camera or upload)
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('❌ Ukuran file terlalu besar! Maksimal 2MB.\n\nTips: Gunakan kualitas foto medium di kamera HP.');
        clearPhoto();
        return;
    }
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!validTypes.includes(file.type)) {
        alert('❌ Format file tidak valid! Gunakan JPG, PNG, atau PDF.');
        clearPhoto();
        return;
    }
    
    currentFile = file;
    
    // Show preview for images
    if (file.type.startsWith('image/')) {
        showPhotoPreview(file);
    } else {
        // For PDF, just show filename
        document.getElementById('photoPreview').style.display = 'block';
        document.getElementById('previewImage').style.display = 'none';
        alert('✅ File PDF berhasil dipilih: ' + file.name);
    }
}

// Show photo preview and extract EXIF
function showPhotoPreview(file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        // Show preview image
        const preview = document.getElementById('previewImage');
        preview.src = e.target.result;
        document.getElementById('photoPreview').style.display = 'block';
        
        // Extract EXIF metadata
        extractEXIF(file);
    };
    
    reader.readAsDataURL(file);
}

// Extract EXIF metadata from photo
function extractEXIF(file) {
    EXIF.getData(file, function() {
        const allMetaData = EXIF.getAllTags(this);
        console.log('EXIF Data:', allMetaData);
        
        let hasMetadata = false;
        let hasGPS = false;
        
        // Extract GPS coordinates
        const lat = EXIF.getTag(this, 'GPSLatitude');
        const latRef = EXIF.getTag(this, 'GPSLatitudeRef');
        const lng = EXIF.getTag(this, 'GPSLongitude');
        const lngRef = EXIF.getTag(this, 'GPSLongitudeRef');
        
        if (lat && lng && latRef && lngRef) {
            const latitude = convertDMSToDD(lat, latRef);
            const longitude = convertDMSToDD(lng, lngRef);
            
            // Display GPS info
            document.getElementById('gpsCoords').textContent = 
                latitude.toFixed(6) + ', ' + longitude.toFixed(6);
            document.getElementById('gpsLink').href = 
                'https://www.google.com/maps?q=' + latitude + ',' + longitude;
            document.getElementById('gpsInfo').style.display = 'block';
            
            // Store in hidden inputs
            document.getElementById('metadata_gps_lat').value = latitude;
            document.getElementById('metadata_gps_lng').value = longitude;
            
            hasMetadata = true;
            hasGPS = true;
        }
        
        // Extract timestamp
        const dateTime = EXIF.getTag(this, 'DateTimeOriginal') || EXIF.getTag(this, 'DateTime');
        if (dateTime) {
            // Format: "2024:01:29 14:30:45" -> "29-01-2024 14:30"
            const formatted = formatEXIFDate(dateTime);
            document.getElementById('photoTimestamp').textContent = formatted;
            document.getElementById('timestampInfo').style.display = 'block';
            document.getElementById('metadata_timestamp').value = dateTime;
            hasMetadata = true;
        }
        
        // Extract device info
        const make = EXIF.getTag(this, 'Make');
        const model = EXIF.getTag(this, 'Model');
        if (make || model) {
            const device = (make || '') + ' ' + (model || '');
            document.getElementById('deviceName').textContent = device.trim();
            document.getElementById('deviceInfo').style.display = 'block';
            document.getElementById('metadata_device').value = device.trim();
            hasMetadata = true;
        }
        
        // FALLBACK: If no GPS in EXIF, try browser Geolocation API
        if (!hasGPS && navigator.geolocation) {
            console.log('No EXIF GPS found, trying browser geolocation...');
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    
                    // Display GPS info with browser icon
                    document.getElementById('gpsCoords').textContent = 
                        latitude.toFixed(6) + ', ' + longitude.toFixed(6) + ' 🌐';
                    document.getElementById('gpsLink').href = 
                        'https://www.google.com/maps?q=' + latitude + ',' + longitude;
                    document.getElementById('gpsInfo').style.display = 'block';
                    
                    // Store in hidden inputs
                    document.getElementById('metadata_gps_lat').value = latitude;
                    document.getElementById('metadata_gps_lng').value = longitude;
                    
                    // Show metadata display
                    document.getElementById('metadataDisplay').style.display = 'block';
                    document.getElementById('noGpsWarning').style.display = 'none';
                    
                    console.log('✅ Browser GPS injected:', latitude, longitude);
                },
                function(error) {
                    console.warn('Browser geolocation failed:', error);
                    // Show warning if both EXIF and browser GPS failed
                    if (!hasMetadata) {
                        document.getElementById('metadataDisplay').style.display = 'none';
                        document.getElementById('noGpsWarning').style.display = 'block';
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        } else {
            // Show metadata or warning
            if (hasMetadata) {
                document.getElementById('metadataDisplay').style.display = 'block';
                document.getElementById('noGpsWarning').style.display = 'none';
            } else {
                document.getElementById('metadataDisplay').style.display = 'none';
                document.getElementById('noGpsWarning').style.display = 'block';
            }
        }
    });
}

// Convert GPS DMS to Decimal Degrees
function convertDMSToDD(dms, ref) {
    const degrees = dms[0];
    const minutes = dms[1];
    const seconds = dms[2];
    
    let dd = degrees + (minutes / 60) + (seconds / 3600);
    
    if (ref === 'S' || ref === 'W') {
        dd = dd * -1;
    }
    
    return dd;
}

// Format EXIF date
function formatEXIFDate(exifDate) {
    // Input: "2024:01:29 14:30:45"
    // Output: "29-01-2024 14:30"
    const parts = exifDate.split(' ');
    const dateParts = parts[0].split(':');
    const timeParts = parts[1].split(':');
    
    return dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0] + ' ' + 
           timeParts[0] + ':' + timeParts[1];
}

// Clear photo and reset
function clearPhoto() {
    currentFile = null;
    document.getElementById('file_bukti').value = '';
    document.getElementById('photoPreview').style.display = 'none';
    document.getElementById('previewImage').src = '';
    document.getElementById('metadataDisplay').style.display = 'none';
    document.getElementById('noGpsWarning').style.display = 'none';
    
    // Clear hidden inputs
    document.getElementById('metadata_gps_lat').value = '';
    document.getElementById('metadata_gps_lng').value = '';
    document.getElementById('metadata_timestamp').value = '';
    document.getElementById('metadata_device').value = '';
    
    // Reset file input attributes
    const input = document.getElementById('file_bukti');
    input.removeAttribute('capture');
    input.setAttribute('accept', '.jpg,.jpeg,.png,.pdf');
}

// Check GPS/Geolocation status saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    // Check GPS segera saat halaman load
    setTimeout(function() {
        checkGPSStatusProactive();
    }, 1000);
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

<!-- Include jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Initialize Select2 for madrasah dropdown
$(document).ready(function() {
    $('#madrasah_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Madrasah --',
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
</script>


<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
