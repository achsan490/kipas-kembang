<?php
// modules/admin/users/edit.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('admin');

$id = $_GET['id'] ?? 0;

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
if(mysqli_num_rows($query) == 0) {
    flash('danger', 'User tidak ditemukan.');
    redirect('modules/admin/users/index.php');
}

$data = mysqli_fetch_assoc($query);
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Edit User</h1>
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
                    <form action="aksi.php?act=update" method="POST">
                        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="nip" class="form-label fw-bold">NIP / Username</label>
                            <input type="text" class="form-control bg-light border-0" id="nip" name="nip" value="<?php echo $data['nip']; ?>" required readonly>
                            <div class="form-text">NIP tidak dapat diubah</div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control bg-light border-0" id="nama_lengkap" name="nama_lengkap" value="<?php echo $data['nama_lengkap']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password Baru <span class="text-muted fw-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
                            <input type="password" class="form-control bg-light border-0" id="password" name="password" placeholder="Masukkan password baru">
                            <div class="form-text">Minimal 6 karakter</div>
                        </div>

                        <div class="mb-3">
                            <label for="jabatan" class="form-label fw-bold">Jabatan</label>
                            <input type="text" class="form-control bg-light border-0" id="jabatan" name="jabatan" value="<?php echo $data['jabatan']; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label fw-bold">Role / Hak Akses</label>
                            <select class="form-select bg-light border-0" id="role" name="role" required>
                                <option value="pengawas" <?php echo ($data['role'] == 'pengawas') ? 'selected' : ''; ?>>Pengawas</option>
                                <option value="pimpinan" <?php echo ($data['role'] == 'pimpinan') ? 'selected' : ''; ?>>Pimpinan</option>
                                <option value="admin" <?php echo ($data['role'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
