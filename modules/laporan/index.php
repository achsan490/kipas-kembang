<?php
// modules/laporan/index.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../templates/sidebar.php';

checkRole('pimpinan'); // Atau admin

// FILTER
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$user_id = $_GET['user_id'] ?? '';

// Build Query
$where = "WHERE k.status = 'disetujui' AND MONTH(k.tanggal_kegiatan) = '$bulan' AND YEAR(k.tanggal_kegiatan) = '$tahun'";
if ($user_id) {
    $where .= " AND k.user_id = $user_id";
}

$query = "SELECT k.*, u.nama_lengkap, u.nip, jk.nama_kegiatan, jk.poin_kredit
          FROM kinerja k 
          JOIN users u ON k.user_id = u.id
          JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
          $where
          ORDER BY k.tanggal_kegiatan ASC";

$result = mysqli_query($conn, $query);

// Ambil list pengawas untuk filter
$q_users = mysqli_query($conn, "SELECT id, nama_lengkap FROM users WHERE role = 'pengawas'");
?>

<main class="main-content w-100">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold text-dark">Laporan Kinerja Pengawas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button onclick="window.print()" class="btn btn-outline-primary rounded-pill shadow-sm">
                <i class="fas fa-print me-2"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 no-print border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Bulan</label>
                    <select name="bulan" class="form-select bg-light border-0">
                        <?php 
                        $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        foreach($months as $k => $v) {
                            $sel = ($k == $bulan) ? 'selected' : '';
                            echo "<option value='$k' $sel>$v</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Tahun</label>
                    <select name="tahun" class="form-select bg-light border-0">
                        <?php 
                        for($y=date('Y'); $y>=date('Y')-5; $y--) {
                            $sel = ($y == $tahun) ? 'selected' : '';
                            echo "<option value='$y' $sel>$y</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Pengawas</label>
                    <select name="user_id" class="form-select bg-light border-0">
                        <option value="">-- Semua Pengawas --</option>
                        <?php while($u = mysqli_fetch_assoc($q_users)): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $user_id) ? 'selected' : ''; ?>>
                                <?php echo $u['nama_lengkap']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100 rounded-pill"><i class="fas fa-filter me-2"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="bg-light text-center">
                        <tr>
                            <th class="py-3">No</th>
                            <th class="py-3">Nama Pengawas</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Uraian Kegiatan</th>
                            <th class="py-3">Poin Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        $total_poin = 0;
                        while($row = mysqli_fetch_assoc($result)): 
                            $total_poin += $row['poin_kredit'];
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td class="text-center"><?php echo date('d-m-Y', strtotime($row['tanggal_kegiatan'])); ?></td>
                            <td>
                                <strong><?php echo $row['nama_kegiatan']; ?></strong><br>
                                <?php echo $row['deskripsi']; ?>
                            </td>
                            <td class="text-center fw-bold text-success"><?php echo $row['poin_kredit']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if(mysqli_num_rows($result) > 0): ?>
                        <tr class="table-info fw-bold">
                            <td colspan="4" class="text-end pe-3">Total Poin Kredit</td>
                            <td class="text-center"><?php echo $total_poin; ?></td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Tidak ada data laporan untuk periode ini.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
@media print {
    .no-print, .sidebar, .navbar {
        display: none !important;
    }
    main {
        width: 100%;
        margin: 0;
        padding: 0;
    }
}
</style>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
