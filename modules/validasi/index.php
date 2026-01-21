<?php
// modules/validasi/index.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

// Cek akses: hanya pimpinan atau admin
if ($_SESSION['role'] !== 'pimpinan' && $_SESSION['role'] !== 'admin') {
    die("Akses ditolak");
}

$query = "SELECT k.*, u.nama_lengkap, u.nip, jk.nama_kegiatan, m.nama_madrasah 
          FROM kinerja k 
          JOIN users u ON k.user_id = u.id
          JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
          LEFT JOIN madrasah m ON k.madrasah_id = m.id
          WHERE k.status = 'pending'
          ORDER BY k.tanggal_kegiatan ASC";
$result = mysqli_query($conn, $query);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Validasi Kinerja</h1>
    </div>

    <?php echo flash(); ?>

    <div class="alert alert-info border-0 shadow-sm rounded-3">
        <i class="fas fa-info-circle me-2"></i> Berikut adalah laporan kinerja pengawas yang menunggu persetujuan Anda.
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive table-responsive-card">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4">No</th>
                            <th class="py-3">Nama Pengawas</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Kegiatan</th>
                            <th class="py-3">Deskripsi & Bukti</th>
                            <th class="py-3 pe-4 text-center">Aksi Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-4" data-label="No"><?php echo $no++; ?></td>
                            <td data-label="Nama Pengawas">
                                <strong class="text-dark"><?php echo $row['nama_lengkap']; ?></strong><br>
                                <small class="text-muted">NIP. <?php echo $row['nip']; ?></small>
                            </td>
                            <td data-label="Tanggal"><?php echo date('d-m-Y', strtotime($row['tanggal_kegiatan'])); ?></td>
                            <td data-label="Kegiatan">
                                <?php echo $row['nama_kegiatan']; ?><br>
                                <small class="text-muted"><?php echo $row['nama_madrasah'] ? '<i class="fas fa-map-marker-alt me-1"></i> ' . $row['nama_madrasah'] : ''; ?></small>
                            </td>
                            <td data-label="Deskripsi">
                                <p class="mb-1 text-secondary"><?php echo $row['deskripsi']; ?></p>
                                <?php if($row['file_bukti']): ?>
                                    <a href="<?php echo base_url('uploads/'.$row['file_bukti']); ?>" target="_blank" class="badge bg-primary text-decoration-none rounded-pill px-3 py-2">
                                        <i class="fas fa-paperclip me-1"></i> Lihat Bukti
                                    </a>
                                    
                                    <?php 
                                    // Tampilkan metadata foto jika ada
                                    if ($row['foto_timestamp'] || $row['foto_gps_lat']): 
                                        require_once __DIR__ . '/../../core/exif_helper.php';
                                    ?>
                                        <div class="mt-2 small">
                                            <?php if ($row['foto_timestamp']): ?>
                                                <div class="text-success">
                                                    <i class="fas fa-camera me-1"></i> 
                                                    Foto: <?php echo date('d-m-Y H:i', strtotime($row['foto_timestamp'])); ?>
                                                    <?php
                                                    $validation = validatePhotoTimestamp(
                                                        date('Y:m:d H:i:s', strtotime($row['foto_timestamp'])),
                                                        $row['tanggal_kegiatan']
                                                    );
                                                    if ($validation['warning']):
                                                    ?>
                                                        <span class="badge bg-<?php echo $validation['level']; ?> ms-1">
                                                            <?php echo $validation['warning']; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> Foto tanpa timestamp
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($row['foto_gps_lat'] && $row['foto_gps_lng']): ?>
                                                <div class="text-info mt-1">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <a href="<?php echo getGoogleMapsLink($row['foto_gps_lat'], $row['foto_gps_lng']); ?>" target="_blank" class="text-decoration-none">
                                                        GPS: <?php echo number_format($row['foto_gps_lat'], 6); ?>, <?php echo number_format($row['foto_gps_lng'], 6); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill">Tanpa Bukti</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Aksi" class="pe-4 text-center">
                                <form action="aksi.php" method="POST" class="d-flex gap-2 justify-content-end justify-content-md-center">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="status" value="disetujui" class="btn btn-success btn-sm rounded-pill px-3" onclick="return confirm('Setujui?')">
                                        <i class="fas fa-check me-1"></i> Terima
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTolak<?php echo $row['id']; ?>">
                                        <i class="fas fa-times me-1"></i> Tolak
                                    </button>

                                    <!-- Modal Tolak -->
                                    <div class="modal fade" id="modalTolak<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Alasan Penolakan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <textarea name="catatan" class="form-control" placeholder="Berikan alasan kenapa ditolak..." rows="3"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="status" value="ditolak" class="btn btn-danger">Kirim Penolakan</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if(mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Tidak ada data pending. Semua aman!</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
