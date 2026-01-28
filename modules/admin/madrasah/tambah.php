<?php
// modules/admin/madrasah/tambah.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../templates/sidebar.php';

checkRole('admin');
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Madrasah</h1>
    </div>

    <div class="col-md-6">
        <form action="aksi.php?act=insert" method="POST">
            <div class="mb-3">
                <label class="form-label">NSM (Nomor Statistik Madrasah)</label>
                <input type="text" name="nsm" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Madrasah</label>
                <input type="text" name="nama_madrasah" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kepala Madrasah</label>
                <input type="text" name="kepala_madrasah" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan Data</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>
