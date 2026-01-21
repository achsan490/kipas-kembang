-- Database: `kinerja_pengawas`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

-- Buat Database jika belum ada
CREATE DATABASE IF NOT EXISTS `kinerja_pengawas`;
USE `kinerja_pengawas`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pengawas','pimpinan') NOT NULL DEFAULT 'pengawas',
  `jabatan` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nip` (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
-- Password default: 123456 (akan di-hash di aplikasi, ini contoh script insert manual dengan hash dummy jika perlu, tapi sebaiknya insert lewat app atau seeder)
-- Untuk contoh ini kita biarkan kosong atau insert satu admin default nanti.

INSERT INTO `users` (`nip`, `nama_lengkap`, `password`, `role`, `jabatan`) VALUES
('admin', 'Administrator Sistem', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'IT Support'),
('198001012005011001', 'H. Ahmad Fauzi, M.Pd.I', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas Madrasah Aliyah'),
('197512122000031002', 'Dr. Siti Aminah, M.Ag', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pimpinan', 'Kepala Seksi Pendma');

-- Password untuk semua akun di atas adalah 'password' (hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)

-- --------------------------------------------------------

--
-- Table structure for table `jenis_kegiatan`
--

CREATE TABLE `jenis_kegiatan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kegiatan` varchar(255) NOT NULL,
  `poin_kredit` float DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `jenis_kegiatan` (`nama_kegiatan`, `poin_kredit`) VALUES
('Pemantauan 8 Standar Nasional Pendidikan', 2),
('Penilaian Kinerja Guru (PKG)', 3),
('Pembimbingan dan Pelatihan Guru', 2.5),
('Penyusunan Program Pengawasan', 1),
('Laporan Hasil Pengawasan', 1.5);

-- --------------------------------------------------------

--
-- Table structure for table `madrasah`
--

CREATE TABLE `madrasah` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nsm` varchar(20) NOT NULL,
  `nama_madrasah` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kepala_madrasah` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nsm` (`nsm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `madrasah` (`nsm`, `nama_madrasah`, `alamat`, `kepala_madrasah`) VALUES
('111135170001', 'MAN 1 Jombang', 'Jl. Mastrip No. 5 Jombang', 'Erma Fatmawati'),
('121235170002', 'MTsN 3 Jombang', 'Tambakberas Jombang', 'Moch. Syuaib');

-- --------------------------------------------------------

--
-- Table structure for table `kinerja`
--

CREATE TABLE `kinerja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `jenis_kegiatan_id` int(11) NOT NULL,
  `madrasah_id` int(11) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `file_bukti` varchar(255) DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `catatan_validasi` text DEFAULT NULL,
  `tanggal_validasi` datetime DEFAULT NULL,
  `validator_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `jenis_kegiatan_id` (`jenis_kegiatan_id`),
  KEY `madrasah_id` (`madrasah_id`),
  CONSTRAINT `fk_kinerja_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kinerja_kegiatan` FOREIGN KEY (`jenis_kegiatan_id`) REFERENCES `jenis_kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kinerja_madrasah` FOREIGN KEY (`madrasah_id`) REFERENCES `madrasah` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
