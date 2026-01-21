<?php
// core/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . base_url('modules/auth/login.php'));
        exit;
    }
}

// Cek apakah user memiliki role tertentu
// Cek apakah user memiliki role tertentu
function checkRole($role) {
    checkLogin();
    // Admin boleh akses semua level
    if ($_SESSION['role'] === 'admin') {
        return true;
    }
    
    if ($_SESSION['role'] !== $role) {
        // Jika user tidak punya akses
        echo "Akses Ditolak! <a href='" . base_url('modules/dashboard/index.php') . "'>Kembali ke Dashboard</a>";
        exit;
    }
}

// Ambil data user dari session
function user() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'nama' => $_SESSION['nama'] ?? 'Guest',
        'role' => $_SESSION['role'] ?? null,
        'nip' => $_SESSION['nip'] ?? null
    ];
}
