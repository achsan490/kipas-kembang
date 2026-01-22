-- ========================================
-- Script: Create Table Pengawas Madrasah
-- Deskripsi: Tabel relasi untuk assignment pengawas ke madrasah tertentu
-- Tanggal: 2026-01-22
-- ========================================

USE `kinerja_pengawas`;

-- --------------------------------------------------------
-- Table structure for table `pengawas_madrasah`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `pengawas_madrasah` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pengawas_id` int(11) NOT NULL COMMENT 'ID user dengan role pengawas',
  `madrasah_id` int(11) NOT NULL COMMENT 'ID madrasah yang diawasi',
  `tanggal_penugasan` date NOT NULL COMMENT 'Tanggal mulai penugasan',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif' COMMENT 'Status penugasan',
  `keterangan` text DEFAULT NULL COMMENT 'Catatan tambahan tentang penugasan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_pengawas_madrasah` (`pengawas_id`, `madrasah_id`),
  KEY `idx_pengawas` (`pengawas_id`),
  KEY `idx_madrasah` (`madrasah_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_pm_pengawas` FOREIGN KEY (`pengawas_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pm_madrasah` FOREIGN KEY (`madrasah_id`) REFERENCES `madrasah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel assignment pengawas ke madrasah';

-- --------------------------------------------------------
-- Migrasi Data Existing (jika ada data kinerja)
-- --------------------------------------------------------

-- Insert assignment berdasarkan data kinerja yang sudah ada
-- Ini akan membuat assignment otomatis untuk pengawas yang sudah pernah input kinerja di madrasah tertentu
INSERT IGNORE INTO pengawas_madrasah (pengawas_id, madrasah_id, tanggal_penugasan, status, keterangan)
SELECT DISTINCT 
    k.user_id as pengawas_id,
    k.madrasah_id,
    MIN(k.tanggal_kegiatan) as tanggal_penugasan,
    'aktif' as status,
    'Migrasi otomatis dari data kinerja existing' as keterangan
FROM kinerja k
INNER JOIN users u ON k.user_id = u.id
WHERE u.role = 'pengawas' 
  AND k.madrasah_id IS NOT NULL
GROUP BY k.user_id, k.madrasah_id;

-- --------------------------------------------------------
-- Sample Data untuk Testing
-- --------------------------------------------------------

-- Contoh assignment untuk pengawas yang ada
-- NIP: 198001012005011001 (H. Ahmad Fauzi) mengawasi MAN 1 Jombang dan MTsN 3 Jombang
INSERT IGNORE INTO pengawas_madrasah (pengawas_id, madrasah_id, tanggal_penugasan, status, keterangan)
SELECT 
    u.id as pengawas_id,
    m.id as madrasah_id,
    '2026-01-01' as tanggal_penugasan,
    'aktif' as status,
    'Assignment awal tahun 2026' as keterangan
FROM users u
CROSS JOIN madrasah m
WHERE u.nip = '198001012005011001'
  AND m.nsm IN ('111135170001', '121235170002');

-- --------------------------------------------------------
-- Verification Query
-- --------------------------------------------------------

-- Cek hasil assignment
SELECT 
    pm.id,
    u.nama_lengkap as pengawas,
    u.nip,
    m.nama_madrasah,
    m.nsm,
    pm.tanggal_penugasan,
    pm.status,
    pm.keterangan
FROM pengawas_madrasah pm
INNER JOIN users u ON pm.pengawas_id = u.id
INNER JOIN madrasah m ON pm.madrasah_id = m.id
ORDER BY u.nama_lengkap, m.nama_madrasah;

COMMIT;
