<?php
// modules/laporan/export_excel.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/../../core/functions.php';

checkLogin();
$role = $_SESSION['role'] ?? '';

// Pastikan role yang diizinkan: pimpinan, admin, pengawas
if ($role !== 'pimpinan' && $role !== 'admin' && $role !== 'pengawas') {
    exit('Akses Ditolak');
}

// Get filter values
$filters = [
    'tanggal_mulai' => $_GET['tanggal_mulai'] ?? '',
    'tanggal_selesai' => $_GET['tanggal_selesai'] ?? '',
    'pengawas_id' => ($role === 'pengawas') ? $_SESSION['user_id'] : ($_GET['pengawas_id'] ?? ''),
    'madrasah_id' => $_GET['madrasah_id'] ?? '',
    'jenjang' => $_GET['jenjang'] ?? '',
    'kecamatan' => $_GET['kecamatan'] ?? '',
    'status' => $_GET['status'] ?? 'disetujui',
    'search_pengawas' => $_GET['search_pengawas'] ?? ''
];

// Get laporan data
$laporan = getLaporanWithFilter($conn, $filters);

// Set filename
$filename = "Laporan_Kinerja_" . date('Ymd_His') . ".xls";

// Headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Periode string
$periode_str = 'Semua Periode';
if ($filters['tanggal_mulai'] && $filters['tanggal_selesai']) {
    $periode_str = date('d-m-Y', strtotime($filters['tanggal_mulai'])) . ' s/d ' . date('d-m-Y', strtotime($filters['tanggal_selesai']));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <div style="text-align: center;">
        <h3>LAPORAN KINERJA PENGAWAS</h3>
        <h4>KANTOR KEMENTERIAN AGAMA KABUPATEN JOMBANG</h4>
        <p>Periode: <?php echo $periode_str; ?></p>
    </div>

    <table border="1">
        <thead>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th>No</th>
                <th>NIP</th>
                <th>Nama Pengawas</th>
                <th>Tanggal</th>
                <th>Madrasah</th>
                <th>Jenjang</th>
                <th>Kecamatan</th>
                <th>Kegiatan</th>
                <th>Deskripsi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($laporan['data']) > 0): ?>
                <?php $no = 1; foreach ($laporan['data'] as $item): ?>
                <tr>
                    <td align="center"><?php echo $no++; ?></td>
                    <td>'<?php echo $item['nip']; ?></td> <!-- Apostrophe to force string in Excel -->
                    <td><?php echo $item['pengawas_nama']; ?></td>
                    <td align="center"><?php echo date('d-m-Y', strtotime($item['tanggal_kegiatan'])); ?></td>
                    <td><?php echo $item['nama_madrasah'] ?? '-'; ?></td>
                    <td align="center"><?php echo $item['jenjang'] ?? '-'; ?></td>
                    <td><?php echo $item['kecamatan'] ?? '-'; ?></td>
                    <td><?php echo $item['nama_kegiatan']; ?></td>
                    <td><?php echo $item['deskripsi']; ?></td>
                    <td align="center"><?php echo ucfirst($item['status']); ?></td>
                </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="10" align="center">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <table width="100%">
            <tr>
                <td width="70%"></td>
                <td width="30%" align="center">
                    <p>Jombang, <?php echo date('d F Y'); ?></p>
                    <br><br><br>
                    <?php if ($role === 'pengawas'): ?>
                        <p><b><u><?php echo $_SESSION['nama']; ?></u></b></p>
                        <p>NIP. <?php echo $_SESSION['nip'] ?? '-'; ?></p>
                    <?php else: ?>
                        <p><b><u>Dr. Siti Aminah, M.Ag</u></b></p>
                        <p>NIP. 197512122000031002</p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
