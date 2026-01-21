<?php
// modules/admin/madrasah/index.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('admin');

$result = mysqli_query($conn, "SELECT * FROM madrasah ORDER BY nama_madrasah ASC");
?>
<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Data Madrasah</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="tambah.php" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Madrasah
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
                            <th class="py-3">NSM</th>
                            <th class="py-3">Nama Madrasah</th>
                            <th class="py-3">Kepala Madrasah</th>
                            <th class="py-3">Alamat</th>
                            <th class="py-3 pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-4" data-label="No"><?php echo $no++; ?></td>
                            <td data-label="NSM" class="fw-semibold text-primary"><?php echo $row['nsm']; ?></td>
                            <td data-label="Nama Madrasah" class="fw-bold text-dark"><?php echo $row['nama_madrasah']; ?></td>
                            <td data-label="Kepala"><?php echo $row['kepala_madrasah']; ?></td>
                            <td data-label="Alamat" class="text-muted small"><?php echo $row['alamat']; ?></td>
                            <td data-label="Aksi" class="pe-4 text-end">
                                <a href="#" onclick="confirmDelete('aksi.php?act=delete&id=<?php echo $row['id']; ?>')" class="btn btn-sm btn-danger rounded-circle"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
