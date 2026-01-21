-- Update tabel kinerja untuk menambahkan kolom metadata foto
ALTER TABLE `kinerja` 
ADD COLUMN `foto_timestamp` DATETIME NULL COMMENT 'Waktu asli foto diambil dari EXIF',
ADD COLUMN `foto_gps_lat` DECIMAL(10, 8) NULL COMMENT 'GPS Latitude',
ADD COLUMN `foto_gps_lng` DECIMAL(11, 8) NULL COMMENT 'GPS Longitude',
ADD COLUMN `foto_metadata` TEXT NULL COMMENT 'JSON metadata lengkap EXIF';
