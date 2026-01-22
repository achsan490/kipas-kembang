-- ========================================
-- Script: Clean Duplicates and Update Constraint
-- Deskripsi: Bersihkan duplikat dan enforce satu madrasah = satu pengawas aktif
-- Tanggal: 2026-01-22
-- ========================================

USE `kinerja_pengawas`;

-- Step 1: Cek duplikat yang ada
SELECT 
    madrasah_id, 
    status, 
    COUNT(*) as jumlah_duplikat,
    GROUP_CONCAT(id) as assignment_ids
FROM pengawas_madrasah 
GROUP BY madrasah_id, status 
HAVING COUNT(*) > 1;

-- Step 2: Hapus duplikat (keep yang paling baru berdasarkan created_at)
-- Ini akan menghapus assignment lama jika ada duplikat
DELETE pm1 FROM pengawas_madrasah pm1
INNER JOIN pengawas_madrasah pm2 
WHERE pm1.madrasah_id = pm2.madrasah_id 
  AND pm1.status = pm2.status
  AND pm1.id < pm2.id;

-- Step 3: Drop constraint lama
ALTER TABLE `pengawas_madrasah` DROP INDEX `unique_pengawas_madrasah`;

-- Step 4: Tambah constraint baru
ALTER TABLE `pengawas_madrasah`
ADD UNIQUE KEY `unique_madrasah_aktif` (`madrasah_id`, `status`);

-- Verifikasi constraint
SHOW INDEX FROM `pengawas_madrasah`;

COMMIT;
