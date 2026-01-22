<?php
// modules/admin/pengawasan/aksi.php
require_once __DIR__ . '/../../../core/koneksi.php';
require_once __DIR__ . '/../../../core/auth.php';
require_once __DIR__ . '/../../../core/functions.php';

checkRole('admin');
$act = $_GET['act'] ?? '';

if ($act == 'insert' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $pengawas_id = intval($_POST['pengawas_id']);
    $madrasah_ids = $_POST['madrasah_ids'] ?? [];
    $tanggal_penugasan = $_POST['tanggal_penugasan'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');
    
    // Validasi
    if (empty($madrasah_ids)) {
        flash('danger', 'Pilih minimal 1 madrasah untuk ditugaskan!');
        redirect('modules/admin/pengawasan/tambah.php');
    }
    
    // Validasi pengawas exists dan role = pengawas
    $check_pengawas = mysqli_query($conn, "SELECT id FROM users WHERE id = $pengawas_id AND role = 'pengawas'");
    if (mysqli_num_rows($check_pengawas) == 0) {
        flash('danger', 'Pengawas tidak valid!');
        redirect('modules/admin/pengawasan/tambah.php');
    }
    
    // Insert multiple assignments
    $success_count = 0;
    $duplicate_count = 0;
    $errors = [];
    
    foreach ($madrasah_ids as $madrasah_id) {
        $madrasah_id = intval($madrasah_id);
        
        // Cek apakah madrasah ini sudah punya pengawas aktif (dari pengawas manapun)
        $check_existing = mysqli_query($conn, 
            "SELECT u.nama_lengkap 
             FROM pengawas_madrasah pm
             INNER JOIN users u ON pm.pengawas_id = u.id
             WHERE pm.madrasah_id = $madrasah_id AND pm.status = 'aktif'"
        );
        
        if (mysqli_num_rows($check_existing) > 0) {
            $existing_pengawas = mysqli_fetch_assoc($check_existing);
            // Madrasah sudah punya pengawas aktif, skip
            $duplicate_count++;
            
            // Get nama madrasah untuk pesan error yang lebih jelas
            $madrasah_info = mysqli_query($conn, "SELECT nama_madrasah FROM madrasah WHERE id = $madrasah_id");
            $madrasah_data = mysqli_fetch_assoc($madrasah_info);
            $errors[] = "{$madrasah_data['nama_madrasah']} sudah diawasi oleh {$existing_pengawas['nama_lengkap']}";
            continue;
        }
        
        // Cek apakah sudah ada assignment untuk pengawas ini ke madrasah ini (untuk historis)
        $check_duplicate = mysqli_query($conn, 
            "SELECT id FROM pengawas_madrasah 
             WHERE pengawas_id = $pengawas_id AND madrasah_id = $madrasah_id"
        );
        
        if (mysqli_num_rows($check_duplicate) > 0) {
            $duplicate_count++;
            continue;
        }
        
        $query = "INSERT INTO pengawas_madrasah 
                  (pengawas_id, madrasah_id, tanggal_penugasan, status, keterangan) 
                  VALUES ($pengawas_id, $madrasah_id, '$tanggal_penugasan', 'aktif', '$keterangan')";
        
        if (mysqli_query($conn, $query)) {
            $success_count++;
        } else {
            $errors[] = mysqli_error($conn);
        }
    }
    
    // Flash message berdasarkan hasil
    if ($success_count > 0) {
        $msg = "✅ Berhasil menambahkan $success_count assignment.";
        if ($duplicate_count > 0) {
            $msg .= " ($duplicate_count madrasah sudah memiliki pengawas)";
        }
        flash('success', $msg);
        
        // Tampilkan detail error jika ada
        if (!empty($errors)) {
            flash('warning', '⚠️ Beberapa madrasah tidak bisa di-assign:<br>• ' . implode('<br>• ', $errors));
        }
    } else {
        if (!empty($errors)) {
            flash('danger', '❌ Tidak ada assignment yang berhasil ditambahkan:<br>• ' . implode('<br>• ', $errors));
        } else {
            flash('danger', 'Gagal menambahkan assignment. Semua madrasah sudah memiliki pengawas.');
        }
    }
    
    redirect('modules/admin/pengawasan/index.php');

} elseif ($act == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $tanggal_penugasan = $_POST['tanggal_penugasan'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');
    
    // Validasi status
    if (!in_array($status, ['aktif', 'nonaktif'])) {
        flash('danger', 'Status tidak valid!');
        redirect('modules/admin/pengawasan/edit.php?id=' . $id);
    }
    
    $query = "UPDATE pengawas_madrasah SET 
              status = '$status',
              tanggal_penugasan = '$tanggal_penugasan',
              keterangan = '$keterangan'
              WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        flash('success', 'Assignment berhasil diupdate.');
    } else {
        flash('danger', 'Gagal update: ' . mysqli_error($conn));
    }
    
    redirect('modules/admin/pengawasan/index.php');

} elseif ($act == 'delete') {
    $id = intval($_GET['id']);
    
    // Get info sebelum hapus untuk flash message
    $info_query = mysqli_query($conn, 
        "SELECT u.nama_lengkap, m.nama_madrasah 
         FROM pengawas_madrasah pm
         INNER JOIN users u ON pm.pengawas_id = u.id
         INNER JOIN madrasah m ON pm.madrasah_id = m.id
         WHERE pm.id = $id"
    );
    
    if ($info = mysqli_fetch_assoc($info_query)) {
        if (mysqli_query($conn, "DELETE FROM pengawas_madrasah WHERE id = $id")) {
            flash('success', "Assignment {$info['nama_lengkap']} → {$info['nama_madrasah']} berhasil dihapus.");
        } else {
            flash('danger', 'Gagal menghapus: ' . mysqli_error($conn));
        }
    } else {
        flash('danger', 'Data tidak ditemukan!');
    }
    
    redirect('modules/admin/pengawasan/index.php');

} else {
    redirect('modules/admin/pengawasan/index.php');
}
