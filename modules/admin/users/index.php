<?php
// modules/admin/users/index.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('admin');

$query = "SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC";
$result = mysqli_query($conn, $query);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Kelola Pengguna</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="tambah.php" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah User Baru
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
                            <th class="py-3">NIP / Username</th>
                            <th class="py-3">Nama Lengkap</th>
                            <th class="py-3">Jabatan</th>
                            <th class="py-3">Role</th>
                            <th class="py-3 pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-4" data-label="No"><?php echo $no++; ?></td>
                            <td data-label="NIP" class="fw-semibold"><?php echo $row['nip']; ?></td>
                            <td data-label="Nama"><?php echo $row['nama_lengkap']; ?></td>
                            <td data-label="Jabatan"><?php echo $row['jabatan']; ?></td>
                            <td data-label="Role">
                                <span class="badge rounded-pill px-3 bg-<?php echo ($row['role']=='admin'?'danger':($row['role']=='pimpinan'?'warning':'info')); ?>">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td data-label="Aksi" class="pe-4 text-end">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning rounded-circle"><i class="fas fa-edit"></i></a>
                                <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <a href="#" onclick="confirmDelete('aksi.php?act=delete&id=<?php echo $row['id']; ?>')" class="btn btn-sm btn-danger rounded-circle"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
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
