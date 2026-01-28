<?php
// core/exif_helper.php
// Helper functions untuk ekstraksi metadata foto (EXIF)

/**
 * Ekstrak metadata dari foto (EXIF data)
 * @param string $file_path Path lengkap ke file foto
 * @return array|false Array metadata atau false jika gagal
 */
function extractPhotoMetadata($file_path) {
    // Cek apakah file ada
    if (!file_exists($file_path)) {
        return false;
    }
    
    // Cek apakah file adalah gambar
    $mime_type = mime_content_type($file_path);
    if (!in_array($mime_type, ['image/jpeg', 'image/jpg'])) {
        return false; // Hanya support JPEG yang punya EXIF
    }
    
    // Cek apakah EXIF extension tersedia
    if (!function_exists('exif_read_data')) {
        return false;
    }
    
    // Baca EXIF data
    $exif = @exif_read_data($file_path, 0, true);
    
    if (!$exif) {
        return false;
    }
    
    $metadata = [
        'has_exif' => true,
        'timestamp' => null,
        'gps_lat' => null,
        'gps_lng' => null,
        'camera' => null,
        'raw_data' => []
    ];
    
    // Ekstrak timestamp foto
    if (isset($exif['EXIF']['DateTimeOriginal'])) {
        $metadata['timestamp'] = $exif['EXIF']['DateTimeOriginal'];
    } elseif (isset($exif['IFD0']['DateTime'])) {
        $metadata['timestamp'] = $exif['IFD0']['DateTime'];
    }
    
    // Ekstrak GPS data
    if (isset($exif['GPS'])) {
        $gps = $exif['GPS'];
        
        // Konversi GPS ke decimal degrees
        if (isset($gps['GPSLatitude']) && isset($gps['GPSLatitudeRef']) &&
            isset($gps['GPSLongitude']) && isset($gps['GPSLongitudeRef'])) {
            
            $metadata['gps_lat'] = gpsToDecimal($gps['GPSLatitude'], $gps['GPSLatitudeRef']);
            $metadata['gps_lng'] = gpsToDecimal($gps['GPSLongitude'], $gps['GPSLongitudeRef']);
        }
    }
    
    // Ekstrak info kamera
    if (isset($exif['IFD0']['Make']) && isset($exif['IFD0']['Model'])) {
        $metadata['camera'] = trim($exif['IFD0']['Make']) . ' ' . trim($exif['IFD0']['Model']);
    }
    
    // Simpan raw data untuk referensi
    $metadata['raw_data'] = [
        'Make' => $exif['IFD0']['Make'] ?? null,
        'Model' => $exif['IFD0']['Model'] ?? null,
        'DateTime' => $exif['IFD0']['DateTime'] ?? null,
        'DateTimeOriginal' => $exif['EXIF']['DateTimeOriginal'] ?? null,
    ];
    
    return $metadata;
}

/**
 * Konversi GPS coordinates dari format DMS ke Decimal Degrees
 * @param array $coordinate Array [degrees, minutes, seconds]
 * @param string $ref Reference (N/S/E/W)
 * @return float Decimal degrees
 */
function gpsToDecimal($coordinate, $ref) {
    // Konversi fraction ke float
    $degrees = count($coordinate) > 0 ? floatval($coordinate[0]) : 0;
    $minutes = count($coordinate) > 1 ? floatval($coordinate[1]) : 0;
    $seconds = count($coordinate) > 2 ? floatval($coordinate[2]) : 0;
    
    // Hitung decimal
    $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
    
    // Tambahkan tanda negatif untuk South dan West
    if ($ref == 'S' || $ref == 'W') {
        $decimal = -$decimal;
    }
    
    return $decimal;
}

/**
 * Validasi timestamp foto dengan tanggal kegiatan
 * @param string $photo_timestamp Timestamp dari EXIF (format: Y:m:d H:i:s)
 * @param string $activity_date Tanggal kegiatan (format: Y-m-d)
 * @return array Status validasi
 */
function validatePhotoTimestamp($photo_timestamp, $activity_date) {
    if (!$photo_timestamp) {
        return [
            'valid' => false,
            'warning' => 'Foto tidak memiliki informasi waktu (EXIF)',
            'level' => 'warning'
        ];
    }
    
    // Konversi format EXIF (Y:m:d H:i:s) ke timestamp
    $photo_time = strtotime(str_replace(':', '-', substr($photo_timestamp, 0, 10)) . substr($photo_timestamp, 10));
    $activity_time = strtotime($activity_date);
    
    // Hitung selisih hari
    $diff_days = abs(($photo_time - $activity_time) / 86400);
    
    if ($diff_days > 7) {
        return [
            'valid' => false,
            'warning' => 'Foto diambil ' . round($diff_days) . ' hari dari tanggal kegiatan',
            'level' => 'danger'
        ];
    } elseif ($diff_days > 3) {
        return [
            'valid' => true,
            'warning' => 'Foto diambil ' . round($diff_days) . ' hari dari tanggal kegiatan',
            'level' => 'warning'
        ];
    }
    
    return [
        'valid' => true,
        'warning' => null,
        'level' => 'success'
    ];
}

/**
 * Format timestamp EXIF ke format yang readable
 * @param string $exif_timestamp Format: Y:m:d H:i:s
 * @return string Format: d-m-Y H:i
 */
function formatExifTimestamp($exif_timestamp) {
    if (!$exif_timestamp) {
        return '-';
    }
    
    $timestamp = strtotime(str_replace(':', '-', substr($exif_timestamp, 0, 10)) . substr($exif_timestamp, 10));
    return date('d-m-Y H:i', $timestamp);
}

/**
 * Generate Google Maps link dari koordinat GPS
 * @param float $lat Latitude
 * @param float $lng Longitude
 * @return string URL Google Maps
 */
function getGoogleMapsLink($lat, $lng) {
    if (!$lat || !$lng) {
        return null;
    }
    
    return "https://www.google.com/maps?q={$lat},{$lng}";
}
?>
