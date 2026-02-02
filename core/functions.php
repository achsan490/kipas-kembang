<?php
// core/functions.php

// Fungsi untuk mendapatkan base URL agar link tidak broken
// Fungsi untuk mendapatkan base URL agar link tidak broken
function base_url($path = '') {
    // 1. Cek User Config (Constant / Env)
    if (defined('BASE_URL')) {
        $base = BASE_URL;
    } elseif (getenv('BASE_URL')) {
        $base = getenv('BASE_URL');
    } else {
        // 2. Auto-detect
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];

        // Default logic
        if ($host === 'localhost' || $host === '127.0.0.1') {
            $base = "$protocol://$host/kipas"; // Preserves local workflow
        } else {
            $base = "$protocol://$host"; // Assumes root for production
        }
    }

    return rtrim($base, '/') . '/' . ltrim($path, '/');
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
function getAllKegiatan($conn, $role = null) {
    if ($role) {
        $role = mysqli_real_escape_string($conn, $role);
        $query = "SELECT * FROM jenis_kegiatan WHERE target_role = '$role' OR target_role = 'all' ORDER BY nama_kegiatan ASC";
    } else {
        $query = "SELECT * FROM jenis_kegiatan ORDER BY nama_kegiatan ASC";
    }
    
    $result = mysqli_query($conn, $query);
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

// Fungsi Get Madrasah dengan Filter
function getMadrasahWithFilter($conn, $jenjang = null, $kecamatan = null, $status = null) {
    $query = "SELECT * FROM madrasah WHERE 1=1";
    
    if ($jenjang) {
        $jenjang = mysqli_real_escape_string($conn, $jenjang);
        $query .= " AND jenjang = '$jenjang'";
    }
    
    if ($kecamatan) {
        $kecamatan = mysqli_real_escape_string($conn, $kecamatan);
        $query .= " AND kecamatan = '$kecamatan'";
    }
    
    if ($status) {
        $status = mysqli_real_escape_string($conn, $status);
        $query .= " AND status = '$status'";
    }
    
    $query .= " ORDER BY nama_madrasah ASC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi Get Distinct Jenjang
function getAllJenjang($conn) {
    $result = mysqli_query($conn, "SELECT DISTINCT jenjang FROM madrasah WHERE jenjang IS NOT NULL ORDER BY jenjang ASC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row['jenjang'];
    }
    return $data;
}

// Fungsi Get Distinct Kecamatan
function getAllKecamatan($conn) {
    $result = mysqli_query($conn, "SELECT DISTINCT kecamatan FROM madrasah WHERE kecamatan IS NOT NULL ORDER BY kecamatan ASC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row['kecamatan'];
    }
    return $data;
}

// Fungsi Get Statistik Madrasah
function getMadrasahStats($conn) {
    $stats = [];
    
    // Total madrasah
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM madrasah");
    $row = mysqli_fetch_assoc($result);
    $stats['total'] = $row['total'];
    
    // Per jenjang
    $result = mysqli_query($conn, "SELECT jenjang, COUNT(*) as jumlah FROM madrasah GROUP BY jenjang ORDER BY jenjang");
    $stats['per_jenjang'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['per_jenjang'][$row['jenjang']] = $row['jumlah'];
    }
    
    // Per kecamatan (top 10)
    $result = mysqli_query($conn, "SELECT kecamatan, COUNT(*) as jumlah FROM madrasah GROUP BY kecamatan ORDER BY jumlah DESC LIMIT 10");
    $stats['per_kecamatan'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['per_kecamatan'][$row['kecamatan']] = $row['jumlah'];
    }
    
    // Per status
    $result = mysqli_query($conn, "SELECT status, COUNT(*) as jumlah FROM madrasah GROUP BY status");
    $stats['per_status'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['per_status'][$row['status']] = $row['jumlah'];
    }
    
    return $stats;
}

// Fungsi Get Madrasah by ID
function getMadrasahById($conn, $id) {
    $id = intval($id);
    $result = mysqli_query($conn, "SELECT * FROM madrasah WHERE id = $id");
    return mysqli_fetch_assoc($result);
}

// Fungsi Search Madrasah
function searchMadrasah($conn, $keyword) {
    $keyword = mysqli_real_escape_string($conn, $keyword);
    $query = "SELECT * FROM madrasah 
              WHERE nama_madrasah LIKE '%$keyword%' 
              OR nsm LIKE '%$keyword%' 
              OR npsn LIKE '%$keyword%'
              OR alamat LIKE '%$keyword%'
              ORDER BY nama_madrasah ASC
              LIMIT 50";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// ========================================
// DASHBOARD ANALYTICS FUNCTIONS
// ========================================

// Fungsi Get Kinerja per Bulan (12 bulan terakhir)
function getKinerjaMonthly($conn) {
    $query = "SELECT 
                DATE_FORMAT(tanggal_kegiatan, '%Y-%m') as bulan,
                COUNT(*) as jumlah
              FROM kinerja 
              WHERE tanggal_kegiatan >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
              GROUP BY DATE_FORMAT(tanggal_kegiatan, '%Y-%m')
              ORDER BY bulan ASC";
    
    $result = mysqli_query($conn, $query);
    $data = ['labels' => [], 'values' => []];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data['labels'][] = date('M Y', strtotime($row['bulan'] . '-01'));
        $data['values'][] = (int)$row['jumlah'];
    }
    
    return $data;
}

// Fungsi Get Kinerja per Jenjang
function getKinerjaByJenjang($conn) {
    $query = "SELECT 
                COALESCE(m.jenjang, 'Lainnya') as jenjang,
                COUNT(*) as jumlah
              FROM kinerja k
              LEFT JOIN madrasah m ON k.madrasah_id = m.id
              GROUP BY m.jenjang
              ORDER BY jumlah DESC";
    
    $result = mysqli_query($conn, $query);
    $data = ['labels' => [], 'values' => []];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data['labels'][] = $row['jenjang'] ?? 'Lainnya';
        $data['values'][] = (int)$row['jumlah'];
    }
    
    return $data;
}

// Fungsi Get Kinerja per Kecamatan (Top 10)
function getKinerjaByKecamatan($conn) {
    $query = "SELECT 
                COALESCE(m.kecamatan, 'Lainnya') as kecamatan,
                COUNT(*) as jumlah
              FROM kinerja k
              LEFT JOIN madrasah m ON k.madrasah_id = m.id
              GROUP BY m.kecamatan
              ORDER BY jumlah DESC
              LIMIT 10";
    
    $result = mysqli_query($conn, $query);
    $data = ['labels' => [], 'values' => []];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data['labels'][] = $row['kecamatan'] ?? 'Lainnya';
        $data['values'][] = (int)$row['jumlah'];
    }
    
    return $data;
}

// Fungsi Get Top 10 Pengawas Aktif
function getTopPengawas($conn, $limit = 10) {
    $query = "SELECT 
                u.id,
                u.nip,
                u.nama_lengkap,
                COUNT(k.id) as total_kinerja,
                SUM(CASE WHEN k.status = 'disetujui' THEN jk.poin_kredit ELSE 0 END) as total_poin
              FROM users u
              INNER JOIN kinerja k ON u.id = k.user_id
              LEFT JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
              WHERE u.role = 'pengawas'
              GROUP BY u.id, u.nip, u.nama_lengkap
              ORDER BY total_kinerja DESC
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi Get Trend Kinerja (Bulan ini vs Bulan lalu)
function getKinerjaTrend($conn) {
    // Bulan ini
    $query_now = "SELECT COUNT(*) as total FROM kinerja WHERE MONTH(tanggal_kegiatan) = MONTH(NOW()) AND YEAR(tanggal_kegiatan) = YEAR(NOW())";
    $result_now = mysqli_query($conn, $query_now);
    $total_now = mysqli_fetch_assoc($result_now)['total'];
    
    // Bulan lalu
    $query_last = "SELECT COUNT(*) as total FROM kinerja WHERE MONTH(tanggal_kegiatan) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(tanggal_kegiatan) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
    $result_last = mysqli_query($conn, $query_last);
    $total_last = mysqli_fetch_assoc($result_last)['total'];
    
    // Hitung persentase perubahan
    $percentage = 0;
    if ($total_last > 0) {
        $percentage = (($total_now - $total_last) / $total_last) * 100;
    } elseif ($total_now > 0) {
        $percentage = 100;
    }
    
    return [
        'current' => $total_now,
        'previous' => $total_last,
        'percentage' => round($percentage, 1),
        'trend' => $percentage >= 0 ? 'up' : 'down'
    ];
}

// ========================================
// MONITORING PENGAWAS FUNCTIONS
// ========================================

// Fungsi Get Pengawas dengan Statistik
function getPengawasWithStats($conn) {
    $query = "SELECT 
                u.id,
                u.nip,
                u.nama_lengkap,
                u.jabatan,
                COUNT(DISTINCT pm.madrasah_id) as total_madrasah,
                COUNT(k.id) as total_kinerja,
                SUM(CASE WHEN k.status = 'disetujui' THEN jk.poin_kredit ELSE 0 END) as total_poin,
                MAX(k.tanggal_kegiatan) as last_activity
              FROM users u
              LEFT JOIN pengawas_madrasah pm ON u.id = pm.pengawas_id AND pm.status = 'aktif'
              LEFT JOIN kinerja k ON u.id = k.user_id
              LEFT JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
              WHERE u.role = 'pengawas'
              GROUP BY u.id, u.nip, u.nama_lengkap, u.jabatan
              ORDER BY total_kinerja DESC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Hitung status aktif (jika ada kinerja dalam 30 hari terakhir)
        $row['is_active'] = false;
        if ($row['last_activity']) {
            $last = strtotime($row['last_activity']);
            $now = time();
            $diff_days = floor(($now - $last) / (60 * 60 * 24));
            $row['is_active'] = $diff_days <= 30;
            $row['days_inactive'] = $diff_days;
        }
        $data[] = $row;
    }
    return $data;
}

// Fungsi Get Detail Pengawas
function getPengawasDetail($conn, $pengawas_id) {
    $pengawas_id = intval($pengawas_id);
    
    $query = "SELECT 
                u.id,
                u.nip,
                u.nama_lengkap,
                u.jabatan,
                u.created_at,
                COUNT(DISTINCT pm.madrasah_id) as total_madrasah,
                COUNT(k.id) as total_kinerja,
                COUNT(CASE WHEN k.status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN k.status = 'disetujui' THEN 1 END) as approved,
                COUNT(CASE WHEN k.status = 'ditolak' THEN 1 END) as rejected,
                SUM(CASE WHEN k.status = 'disetujui' THEN jk.poin_kredit ELSE 0 END) as total_poin,
                MAX(k.tanggal_kegiatan) as last_activity
              FROM users u
              LEFT JOIN pengawas_madrasah pm ON u.id = pm.pengawas_id AND pm.status = 'aktif'
              LEFT JOIN kinerja k ON u.id = k.user_id
              LEFT JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
              WHERE u.id = $pengawas_id AND u.role = 'pengawas'
              GROUP BY u.id, u.nip, u.nama_lengkap, u.jabatan, u.created_at";
    
    $result = mysqli_query($conn, $query);
    if (!$result || mysqli_num_rows($result) == 0) {
        return null;
    }
    
    $data = mysqli_fetch_assoc($result);
    
    // Get madrasah binaan
    $query_madrasah = "SELECT m.*, pm.tanggal_penugasan 
                       FROM madrasah m
                       INNER JOIN pengawas_madrasah pm ON m.id = pm.madrasah_id
                       WHERE pm.pengawas_id = $pengawas_id AND pm.status = 'aktif'
                       ORDER BY m.nama_madrasah ASC";
    $result_madrasah = mysqli_query($conn, $query_madrasah);
    $data['madrasah_binaan'] = [];
    while ($row = mysqli_fetch_assoc($result_madrasah)) {
        $data['madrasah_binaan'][] = $row;
    }
    
    return $data;
}

// Fungsi Get Timeline Aktivitas Pengawas
function getPengawasTimeline($conn, $pengawas_id, $limit = 10) {
    $pengawas_id = intval($pengawas_id);
    $limit = intval($limit);
    
    $query = "SELECT 
                k.id,
                k.tanggal_kegiatan,
                k.deskripsi,
                k.status,
                k.created_at,
                jk.nama_kegiatan,
                jk.poin_kredit,
                m.nama_madrasah
              FROM kinerja k
              INNER JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
              LEFT JOIN madrasah m ON k.madrasah_id = m.id
              WHERE k.user_id = $pengawas_id
              ORDER BY k.tanggal_kegiatan DESC, k.created_at DESC
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi Get Performa Pengawas (untuk chart)
function getPengawasPerformance($conn, $pengawas_id, $months = 6) {
    $pengawas_id = intval($pengawas_id);
    $months = intval($months);
    
    $query = "SELECT 
                DATE_FORMAT(tanggal_kegiatan, '%Y-%m') as bulan,
                COUNT(*) as jumlah,
                SUM(CASE WHEN status = 'disetujui' THEN jk.poin_kredit ELSE 0 END) as poin
              FROM kinerja k
              LEFT JOIN jenis_kegiatan jk ON k.jenis_kegiatan_id = jk.id
              WHERE k.user_id = $pengawas_id
                AND k.tanggal_kegiatan >= DATE_SUB(NOW(), INTERVAL $months MONTH)
              GROUP BY DATE_FORMAT(tanggal_kegiatan, '%Y-%m')
              ORDER BY bulan ASC";
    
    $result = mysqli_query($conn, $query);
    $data = ['labels' => [], 'kinerja' => [], 'poin' => []];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data['labels'][] = date('M Y', strtotime($row['bulan'] . '-01'));
        $data['kinerja'][] = (int)$row['jumlah'];
        $data['poin'][] = (float)$row['poin'];
    }
    
    return $data;
}

// ========================================
// LAPORAN & EXPORT FUNCTIONS
// ========================================

// Fungsi Get Laporan dengan Filter
function getLaporanWithFilter($conn, $filters = []) {
    $where = [];
    $params = [];
    
    // Filter tanggal (date range)
    if (!empty($filters['tanggal_mulai'])) {
        $where[] = "k.tanggal_kegiatan >= '" . mysqli_real_escape_string($conn, $filters['tanggal_mulai']) . "'";
    }
    if (!empty($filters['tanggal_selesai'])) {
        $where[] = "k.tanggal_kegiatan <= '" . mysqli_real_escape_string($conn, $filters['tanggal_selesai']) . "'";
    }
    
    // Filter pengawas
    if (!empty($filters['pengawas_id'])) {
        $where[] = "k.user_id = " . intval($filters['pengawas_id']);
    }
    
    // Filter madrasah
    if (!empty($filters['madrasah_id'])) {
        $where[] = "k.madrasah_id = " . intval($filters['madrasah_id']);
    }
    
    // Filter jenjang
    if (!empty($filters['jenjang'])) {
        $where[] = "m.jenjang = '" . mysqli_real_escape_string($conn, $filters['jenjang']) . "'";
    }
    
    // Filter kecamatan
    if (!empty($filters['kecamatan'])) {
        $where[] = "m.kecamatan = '" . mysqli_real_escape_string($conn, $filters['kecamatan']) . "'";
    }

    // Filter Nama/NIP Pengawas (Search)
    if (!empty($filters['search_pengawas'])) {
        $search = mysqli_real_escape_string($conn, $filters['search_pengawas']);
        $where[] = "(u.nama_lengkap LIKE '%$search%' OR u.nip LIKE '%$search%')";
    }
    
    // Build WHERE clause
    $where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Query
    $query = "SELECT 
                k.id,
                k.tanggal_kegiatan,
                k.deskripsi,
                k.file_bukti,
                k.created_at,
                u.nip,
                u.nama_lengkap as pengawas_nama,
                m.nsm,
                m.nama_madrasah,
                m.jenjang,
                m.kecamatan
              FROM kinerja k
              INNER JOIN users u ON k.user_id = u.id
              LEFT JOIN madrasah m ON k.madrasah_id = m.id
              $where_clause
              ORDER BY k.tanggal_kegiatan DESC, k.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    $total_kinerja = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
        $total_kinerja++;
    }
    
    return [
        'data' => $data,
        'total_kinerja' => $total_kinerja,
        'filters' => $filters
    ];
}
