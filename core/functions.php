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
