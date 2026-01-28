<?php
// modules/admin/users/aksi.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/functions.php';

checkRole('admin');
$act = $_GET['act'] ?? '';

if ($act == 'insert' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip = $_POST['nip'];
    $nama = $_POST['nama_lengkap'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jabatan = $_POST['jabatan'];
    $role = $_POST['role'];

    // Cek duplicate NIP
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE nip = '$nip'");
    if(mysqli_num_rows($cek) > 0) {
        flash('danger', 'NIP sudah digunakan!');
        redirect('modules/admin/users/tambah.php');
    }

    $q = "INSERT INTO users (nip, nama_lengkap, password, jabatan, role) VALUES ('$nip', '$nama', '$pass', '$jabatan', '$role')";
    if(mysqli_query($conn, $q)) {
        flash('success', 'User berhasil ditambahkan.');
        redirect('modules/admin/users/index.php');
    } else {
        flash('danger', 'Gagal: '.mysqli_error($conn));
        redirect('modules/admin/users/tambah.php');
    }

} elseif ($act == 'delete') {
    $id = $_GET['id'];
    if($id == $_SESSION['user_id']) {
        flash('danger', 'Tidak dapat menghapus akun sendiri.');
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
        flash('success', 'User berhasil dihapus.');
    }
    redirect('modules/admin/users/index.php');

} elseif ($act == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $role = $_POST['role'];
    
    // Cek apakah user ada
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
    if(mysqli_num_rows($cek) == 0) {
        flash('danger', 'User tidak ditemukan.');
        redirect('modules/admin/users/index.php');
    }
    
    // Update password jika diisi
    if(!empty($_POST['password'])) {
        if(strlen($_POST['password']) < 6) {
            flash('danger', 'Password minimal 6 karakter.');
            redirect('modules/admin/users/edit.php?id=' . $id);
        }
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $q = "UPDATE users SET nama_lengkap = '$nama', password = '$pass', jabatan = '$jabatan', role = '$role' WHERE id = $id";
    } else {
        $q = "UPDATE users SET nama_lengkap = '$nama', jabatan = '$jabatan', role = '$role' WHERE id = $id";
    }
    
    if(mysqli_query($conn, $q)) {
        flash('success', 'User berhasil diupdate.');
        redirect('modules/admin/users/index.php');
    } else {
        flash('danger', 'Gagal update: '.mysqli_error($conn));
        redirect('modules/admin/users/edit.php?id=' . $id);
    }
} else {
    redirect('modules/admin/users/index.php');
}
