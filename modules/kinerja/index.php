<?php
// modules/kinerja/index.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

// Pastikan yang akses adalah pengawas atau pendamping
checkLogin();
$allowed_roles = ['pengawas', 'pendamping'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    echo "Akses Ditolak!"; exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT k.*, jk.nama_kegiatan, m.nama_madrasah 
          FROM kinerja k 
          JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
          LEFT JOIN madrasah m ON k.madrasah_id = m.id
          WHERE k.user_id = $user_id
          ORDER BY k.tanggal_kegiatan DESC, k.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Data Kinerja Saya</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="tambah.php" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Kinerja
            </a>
        </div>
    </div>

    <?php echo flash(); ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive table-responsive-card">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4">No</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Kegiatan</th>
                            <th class="py-3">Lokasi</th>
                            <th class="py-3">Deskripsi</th>
                            <th class="py-3">Bukti</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-4" data-label="No"><?php echo $no++; ?></td>
                            <td data-label="Tanggal"><?php echo date('d-m-Y', strtotime($row['tanggal_kegiatan'])); ?></td>
                            <td data-label="Kegiatan">
                                <span class="fw-semibold text-dark"><?php echo $row['nama_kegiatan']; ?></span>
                            </td>
                            <td data-label="Lokasi"><?php echo $row['nama_madrasah'] ? $row['nama_madrasah'] : '-'; ?></td>
                            <td data-label="Deskripsi" class="text-muted small"><?php echo substr($row['deskripsi'], 0, 50) . '...'; ?></td>
                            <td data-label="Bukti">
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
                                                    <?php echo date('d-m-Y H:i', strtotime($row['foto_timestamp'])); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($row['foto_gps_lat'] && $row['foto_gps_lng']): ?>
                                                <div class="text-info mt-1">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <a href="<?php echo getGoogleMapsLink($row['foto_gps_lat'], $row['foto_gps_lng']); ?>" target="_blank" class="text-decoration-none small">
                                                        Lihat Lokasi
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill">Tanpa Bukti</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Status">
                                <?php 
                                if($row['status'] == 'pending') echo '<span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>';
                                elseif($row['status'] == 'disetujui') echo '<span class="badge bg-success rounded-pill px-3">Disetujui</span>';
                                else echo '<span class="badge bg-danger rounded-pill px-3">Ditolak</span>';
                                ?>
                            </td>
                            <td data-label="Aksi" class="pe-4 text-end">
                                <?php if($row['status'] == 'pending' || $row['status'] == 'ditolak'): ?>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning rounded-circle" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="#" onclick="confirmDelete('<?php echo base_url('modules/kinerja/aksi.php?act=delete&id='.$row['id']); ?>')" class="btn btn-sm btn-danger rounded-circle" title="Hapus"><i class="fas fa-trash"></i></a>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-lock"></i></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if(mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">Belum ada data kinerja.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
