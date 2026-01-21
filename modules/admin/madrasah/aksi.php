<?php
// modules/admin/madrasah/aksi.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/functions.php';

checkRole('admin');
$act = $_GET['act'] ?? '';

if ($act == 'insert' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nsm = $_POST['nsm'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_madrasah']);
    $kepala = mysqli_real_escape_string($conn, $_POST['kepala_madrasah']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $q = "INSERT INTO madrasah (nsm, nama_madrasah, kepala_madrasah, alamat) VALUES ('$nsm', '$nama', '$kepala', '$alamat')";
    
    if(mysqli_query($conn, $q)) {
        flash('success', 'Madrasah berhasil ditambahkan.');
        redirect('modules/admin/madrasah/index.php');
    } else {
        flash('danger', 'Gagal: '.mysqli_error($conn));
        redirect('modules/admin/madrasah/tambah.php');
    }

} elseif ($act == 'delete') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM madrasah WHERE id = $id");
    flash('success', 'Data telah dihapus.');
    redirect('modules/admin/madrasah/index.php');
}
