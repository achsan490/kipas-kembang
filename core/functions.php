<?php
// core/functions.php

// Fungsi untuk mendapatkan base URL agar link tidak broken
function base_url($path = '') {
    // Sesuaikan dengan folder project di htdocs/www
    // Jika diakses via http://localhost/kipas/app-kinerja-pengawas/
    $base = "http://" . $_SERVER['HTTP_HOST'] . "/kipas/app-kinerja-pengawas";
    return $base . '/' . ltrim($path, '/');
}

// Fungsi redirect
function redirect($path) {
    header("Location: " . base_url($path));
    exit;
}

// Fungsi flash message (Set & Get)
function flash($type = null, $message = null) {
    if ($type && $message) {
        $_SESSION['flash'] = [
            'type' => $type, // success, danger, warning, info
            'message' => $message
        ];
    } else {
        if (isset($_SESSION['flash'])) {
            $f = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return '<div class="alert alert-' . $f['type'] . ' alert-dismissible fade show" role="alert">
                        ' . $f['message'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }
    }
    return '';
}

// Fungsi Ambil Data Madrasah
function getAllMadrasah($conn) {
    $result = mysqli_query($conn, "SELECT * FROM madrasah ORDER BY nama_madrasah ASC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi Ambil Jenis Kegiatan
function getAllKegiatan($conn) {
    $result = mysqli_query($conn, "SELECT * FROM jenis_kegiatan ORDER BY nama_kegiatan ASC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi Ambil Madrasah yang Di-assign ke Pengawas
function getMadrasahByPengawas($conn, $pengawas_id) {
    $pengawas_id = intval($pengawas_id);
    $query = "SELECT m.* 
              FROM madrasah m
              INNER JOIN pengawas_madrasah pm ON m.id = pm.madrasah_id
              WHERE pm.pengawas_id = $pengawas_id AND pm.status = 'aktif'
              ORDER BY m.nama_madrasah ASC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi Validasi Akses Pengawas ke Madrasah
function validateMadrasahAccess($conn, $pengawas_id, $madrasah_id) {
    $pengawas_id = intval($pengawas_id);
    $madrasah_id = intval($madrasah_id);
    
    $query = "SELECT COUNT(*) as count 
              FROM pengawas_madrasah 
              WHERE pengawas_id = $pengawas_id 
              AND madrasah_id = $madrasah_id 
              AND status = 'aktif'";
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    return $row['count'] > 0;
}

// Fungsi Get All Pengawas
function getAllPengawas($conn) {
    $result = mysqli_query($conn, "SELECT id, nip, nama_lengkap FROM users WHERE role = 'pengawas' ORDER BY nama_lengkap ASC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}
