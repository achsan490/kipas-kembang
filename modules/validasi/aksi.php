<?php
// modules/validasi/aksi.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/../../core/functions.php';

// Validasi akses
if ($_SESSION['role'] !== 'pimpinan' && $_SESSION['role'] !== 'admin') {
    die("Akses ditolak");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status']; // disetujui / ditolak
    $catatan = isset($_POST['catatan']) ? mysqli_real_escape_string($conn, $_POST['catatan']) : '';
    $validator_id = $_SESSION['user_id'];
    $tanggal_validasi = date('Y-m-d H:i:s');

    $query = "UPDATE kinerja SET 
              status = '$status', 
              catatan_validasi = '$catatan', 
              validator_id = $validator_id,
              tanggal_validasi = '$tanggal_validasi'
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        flash('success', "Data kinerja berhasil di-" . $status . ".");
    } else {
        flash('danger', "Gagal memproses data: " . mysqli_error($conn));
    }
}

redirect('modules/validasi/index.php');
