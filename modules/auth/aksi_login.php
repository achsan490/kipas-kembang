<?php
// modules/auth/aksi_login.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/../../core/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/auth/login.php');
}

$nip = mysqli_real_escape_string($conn, trim($_POST['nip']));
$password = trim($_POST['password']);

// Cari user berdasarkan NIP
$query = "SELECT * FROM users WHERE nip = '$nip' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    // Verifikasi password
    // Catatan: Di database.sql tadi saya pakai hash dummy.
    // Jika login gagal dengan hash, pastikan password di DB sesuai.
    // Untuk testing, user 'admin', pass 'password'.
    
    if (password_verify($password, $user['password'])) {
        // Set Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nip'] = $user['nip'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['jabatan'] = $user['jabatan'];
        
        flash('success', 'Selamat datang, ' . $user['nama_lengkap']);
        redirect('modules/dashboard/index.php');
    } else {
        flash('danger', 'Password salah!');
        redirect('modules/auth/login.php');
    }
} else {
    flash('danger', 'NIP tidak ditemukan!');
    redirect('modules/auth/login.php');
}
