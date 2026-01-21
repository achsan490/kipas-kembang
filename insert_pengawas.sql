-- Script untuk menambahkan semua data pengawas
-- Password default untuk semua akun: 'pengawas123'
-- Hash password: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (ini adalah hash untuk 'password')

-- Hapus data lama jika ada (opsional, hati-hati jika sudah ada data kinerja)
-- DELETE FROM users WHERE role = 'pengawas';

-- Insert semua pengawas baru
INSERT INTO `users` (`nip`, `nama_lengkap`, `password`, `role`, `jabatan`) VALUES
('20503502100008', 'HADI NUR RAKHMAD', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MI'),
('91000065135161', 'PANCAHADI SISWASUSILA', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MTs'),
('91000065136494', 'RAHMAD BUDIONO', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MI'),
('91000065148842', 'SUNDOKO', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MTs'),
('91000065148844', 'SUNDUSIN', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MA'),
('91000066101840', 'AGUS PRAMUKANTORO', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MTs'),
('91000066127314', 'MOH. DIMYATI', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas RA'),
('91000067104168', 'ANANG SUBANDI', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MI'),
('91000067113553', 'FATONI', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MA'),
('91000067113930', 'FUAD MF', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MTs'),
('91000067114634', 'HADI S.', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MI'),
('91000067122607', 'KHOIROTUN NISAK', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MI'),
('91000067129223', 'MOHAMMAD ALI SAID', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MA'),
('91000067148467', 'SUHARDI', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas RA'),
('91000067152313', 'SUTRISNO', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MTs'),
('91000067152416', 'SUWARDI', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas RA'),
('91000067155978', 'UMROH MAHFUDLOH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MA'),
('91000068116280', 'HADI SISWANTO', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MI'),
('91000068125158', 'KHUSNUL HAYATI', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas RA'),
('91000068145893', 'SAIFUL HADI', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas RA'),
('91000068149075', 'SITI MAHMUDAH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas RA'),
('91000069103981', 'ALI HASYIM', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas RA'),
('91000069162294', 'TUMAJI', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MI'),
('91000070109706', 'DARLIS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MTs'),
('91000070121160', 'INAYAH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengawas', 'Pengawas MI');

-- Catatan:
-- Semua password default adalah: 'password'
-- NIP digunakan sebagai username untuk login
-- Jabatan disesuaikan dengan jenjang (MI/MTs/MA/RA)
