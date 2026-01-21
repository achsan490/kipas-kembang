<?php
// modules/kinerja/aksi.php
require_once __DIR__ . '/../../core/koneksi.php';
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../../core/exif_helper.php';

checkRole('pengawas');

$act = $_GET['act'] ?? '';
$user_id = $_SESSION['user_id'];

if ($act == 'insert' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal_kegiatan'];
    $jenis_kegiatan = $_POST['jenis_kegiatan_id'];
    $madrasah = !empty($_POST['madrasah_id']) ? $_POST['madrasah_id'] : "NULL";
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Upload File Logic
    $file_bukti = "NULL";
    $foto_timestamp = "NULL";
    $foto_gps_lat = "NULL";
    $foto_gps_lng = "NULL";
    $foto_metadata = "NULL";
    
    if (isset($_FILES['file_bukti']) && $_FILES['file_bukti']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['file_bukti']['name'];
        $filesize = $_FILES['file_bukti']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Check file size (2MB max)
        if ($filesize > 2 * 1024 * 1024) {
            flash('danger', 'Ukuran file terlalu besar. Maksimal 2MB.');
            redirect('modules/kinerja/tambah.php');
        }
        
        if (in_array($ext, $allowed)) {
            $new_name = time() . '_' . $user_id . '.' . $ext;
            $destination = __DIR__ . '/../../uploads/' . $new_name;
            
            // Ensure uploads directory exists
            $upload_dir = __DIR__ . '/../../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['file_bukti']['tmp_name'], $destination)) {
                $file_bukti = "'$new_name'";
                
                // Ekstrak EXIF metadata jika file adalah foto
                if (in_array($ext, ['jpg', 'jpeg'])) {
                    $metadata = extractPhotoMetadata($destination);
                    
                    if ($metadata && $metadata['has_exif']) {
                        // Simpan timestamp
                        if ($metadata['timestamp']) {
                            $timestamp_formatted = str_replace(':', '-', substr($metadata['timestamp'], 0, 10)) . substr($metadata['timestamp'], 10);
                            $foto_timestamp = "'" . date('Y-m-d H:i:s', strtotime($timestamp_formatted)) . "'";
                        }
                        
                        // Simpan GPS
                        if ($metadata['gps_lat'] && $metadata['gps_lng']) {
                            $foto_gps_lat = $metadata['gps_lat'];
                            $foto_gps_lng = $metadata['gps_lng'];
                        }
                        
                        // Simpan metadata lengkap sebagai JSON
                        $foto_metadata = "'" . mysqli_real_escape_string($conn, json_encode($metadata)) . "'";
                        
                        // Validasi timestamp (soft warning)
                        $validation = validatePhotoTimestamp($metadata['timestamp'], $tanggal);
                        if (!$validation['valid'] && $validation['level'] == 'danger') {
                            flash('warning', '⚠️ ' . $validation['warning'] . '. Data tetap disimpan untuk verifikasi pimpinan.');
                        }
                    } else {
                        // Foto tidak punya EXIF - beri warning
                        flash('warning', '⚠️ Foto tidak memiliki informasi waktu/lokasi (EXIF). Disarankan gunakan kamera HP dengan GPS aktif.');
                    }
                }
            } else {
                flash('danger', 'Gagal upload file. Pastikan folder uploads memiliki izin tulis.');
                redirect('modules/kinerja/tambah.php');
            }
        } else {
            flash('danger', 'Format file tidak diizinkan. Gunakan PDF/JPG/PNG.');
            redirect('modules/kinerja/tambah.php');
        }
    }

    $query = "INSERT INTO kinerja (user_id, tanggal_kegiatan, jenis_kegiatan_id, madrasah_id, deskripsi, file_bukti, foto_timestamp, foto_gps_lat, foto_gps_lng, foto_metadata, status) 
              VALUES ($user_id, '$tanggal', $jenis_kegiatan, $madrasah, '$deskripsi', $file_bukti, $foto_timestamp, $foto_gps_lat, $foto_gps_lng, $foto_metadata, 'pending')";

    if (mysqli_query($conn, $query)) {
        flash('success', 'Laporan kinerja berhasil disimpan.');
        redirect('modules/kinerja/index.php');
    } else {
        flash('danger', 'Gagal menyimpan data: ' . mysqli_error($conn));
        redirect('modules/kinerja/tambah.php');
    }

} elseif ($act == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $tanggal = $_POST['tanggal_kegiatan'];
    $jenis_kegiatan = $_POST['jenis_kegiatan_id'];
    $madrasah = !empty($_POST['madrasah_id']) ? $_POST['madrasah_id'] : "NULL";
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Cek kepemilikan dan status
    $cek = mysqli_query($conn, "SELECT * FROM kinerja WHERE id = $id AND user_id = $user_id AND status != 'disetujui'");
    if (mysqli_num_rows($cek) == 0) {
        flash('danger', 'Data tidak ditemukan atau tidak bisa diedit.');
        redirect('modules/kinerja/index.php');
    }
    
    $old_data = mysqli_fetch_assoc($cek);
    $file_bukti = $old_data['file_bukti'] ? "'{$old_data['file_bukti']}'" : "NULL";
    
    // Upload File Logic (jika ada file baru)
    if (isset($_FILES['file_bukti']) && $_FILES['file_bukti']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['file_bukti']['name'];
        $filesize = $_FILES['file_bukti']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Check file size (2MB max)
        if ($filesize > 2 * 1024 * 1024) {
            flash('danger', 'Ukuran file terlalu besar. Maksimal 2MB.');
            redirect('modules/kinerja/edit.php?id=' . $id);
        }
        
        if (in_array($ext, $allowed)) {
            $new_name = time() . '_' . $user_id . '.' . $ext;
            $destination = __DIR__ . '/../../uploads/' . $new_name;
            
            // Ensure uploads directory exists
            $upload_dir = __DIR__ . '/../../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['file_bukti']['tmp_name'], $destination)) {
                // Hapus file lama jika ada
                if ($old_data['file_bukti'] && file_exists(__DIR__ . '/../../uploads/' . $old_data['file_bukti'])) {
                    unlink(__DIR__ . '/../../uploads/' . $old_data['file_bukti']);
                }
                $file_bukti = "'$new_name'";
            } else {
                flash('danger', 'Gagal upload file. Pastikan folder uploads memiliki izin tulis.');
                redirect('modules/kinerja/edit.php?id=' . $id);
            }
        } else {
            flash('danger', 'Format file tidak diizinkan. Gunakan PDF/JPG/PNG.');
            redirect('modules/kinerja/edit.php?id=' . $id);
        }
    }
    
    // Update query - reset status ke pending jika sebelumnya ditolak
    $new_status = ($old_data['status'] == 'ditolak') ? 'pending' : $old_data['status'];
    
    $query = "UPDATE kinerja SET 
              tanggal_kegiatan = '$tanggal',
              jenis_kegiatan_id = $jenis_kegiatan,
              madrasah_id = $madrasah,
              deskripsi = '$deskripsi',
              file_bukti = $file_bukti,
              status = '$new_status'
              WHERE id = $id AND user_id = $user_id";
    
    if (mysqli_query($conn, $query)) {
        flash('success', 'Laporan kinerja berhasil diupdate.');
        redirect('modules/kinerja/index.php');
    } else {
        flash('danger', 'Gagal update data: ' . mysqli_error($conn));
        redirect('modules/kinerja/edit.php?id=' . $id);
    }

} elseif ($act == 'delete') {
    $id = $_GET['id'];
    // Cek kepemilikan
    $cek = mysqli_query($conn, "SELECT * FROM kinerja WHERE id = $id AND user_id = $user_id AND status != 'disetujui'");
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        if ($data['file_bukti']) {
            unlink(__DIR__ . '/../../uploads/' . $data['file_bukti']);
        }
        mysqli_query($conn, "DELETE FROM kinerja WHERE id = $id");
        flash('success', 'Data berhasil dihapus.');
    } else {
        flash('danger', 'Data tidak bisa dihapus (Mungkin sudah disetujui atau bukan milik Anda).');
    }
    redirect('modules/kinerja/index.php');
} else {
    redirect('modules/kinerja/index.php');
}
