# 📊 Aplikasi Kinerja Pengawas (KIPAS) Kementerian Agama Kabupaten Jombang

Sistem informasi terpadu untuk mengelola dan memantau kinerja **Pengawas** dan **Pendamping** madrasah di Kabupaten Jombang, dengan fitur assignment madrasah, validasi berbasis GPS dan timestamp, serta pelaporan komprehensif.

## 🎯 Fitur Utama

### 1. **Unified Role System (Pengawas & Pendamping)**
- ✅ **Satu Database**: Terintegrasi untuk role Pengawas dan Pendamping.
- ✅ **Role Pendamping**: Memiliki akses menu khusus (Input Kinerja, Laporan) yang disesuaikan.
- ✅ **Filtered Activities**: Jenis kegiatan otomatis disesuaikan dengan role yang login.

### 2. **Mobile Responsive Design** 📱 *(New)*
- ✅ **Akses HP/Tablet**: Tampilan otomatis menyesuaikan layar kecil.
- ✅ **Mobile Sidebar**: Menu samping otomatis sembunyi (hidden) dan bisa dibuka dengan tombol hamburger.
- ✅ **Touch Friendly**: Tombol dan navigasi mudah diakses di layar sentuh.

### 3. **Input Kinerja & Validasi**
- ✅ **Assignment**: Satu madrasah hanya diawasi oleh satu pengawas aktif.
- ✅ **Bukti Fisik**: Upload foto/dokumen dengan ekstraksi EXIF (timestamp & GPS location).
- ✅ **Validasi Backend**: Mencegah manipulasi data.
- ✅ **Validasi Pimpinan**: Pimpinan bisa review, approve, atau reject laporan dengan melihat lokasi GPS foto.

### 4. **Dashboard & Laporan**
- ✅ **Smart Dashboard**: Statistik personal untuk Pengawas/Pendamping, dan statistik global untuk Admin/Pimpinan.
- ✅ **Laporan Lengkap**: Filter per periode, jenjang, atau kecamatan.
- ✅ **Export**: Cetak PDF dan Export Excel.

### 5. **Manajemen User**
- ✅ **CRUD User**: Kelola akun Admin, Pengawas, Pendamping, dan Pimpinan.
- ✅ **One Supervisor Policy**: Memastikan tidak ada tumpang tindih pengawasan.

## 🏗️ Teknologi

- **Backend:** PHP Native (7.4+)
- **Database:** MySQL/MariaDB
- **Frontend:** Bootstrap 5, FontAwesome, Chart.js
- **Features:** Responsive Sidebar, GPS Geolocation API, EXIF Reader.

## 📦 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/achsan490/kipas-kembang.git
cd kipas-kembang
```

### 2. Setup Database
1.  Buat database baru bernama `kinerja_pengawas` di phpMyAdmin.
2.  Import file `database.sql` yang ada di root folder project.

### 3. Konfigurasi
Copy file `config/database.php` (jika perlu penyesuaian). Default config:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kinerja_pengawas');
```

### 4. Akses Aplikasi
Buka browser dan akses: `http://localhost/kipas` (sesuaikan dengan nama folder di `htdocs` atau `laragon/www`).

### 5. Akun Default (Login)
| Role | Username (NIP) | Password |
|------|----------------|----------|
| **Admin** | `admin` | `admin123` |
| **Pengawas** | `198001012005011001` | `123456` |
| **Pimpinan** | `197512122000031002` | `123456` |
| **Pendamping** | `1234567890` | `123456` |

## 📝 Changelog

### v2.1.0 (Mobile & Unified) - Current
- ✅ **Mobile Responsive**: Full support untuk akses via HP.
- ✅ **Unified Database**: Gabung database Pengawas & Pendamping.
- ✅ **Role Pendamping**: Fitur input & laporan khusus Pendamping.
- ✅ **UI/UX**: Perbaikan background login, z-index fix, dan dashboard responsiveness.

### v2.0.0
- ✅ Penambahan fitur Pengawas Madrasah (Assignment).
- ✅ One-supervisor-per-madrasah policy.
- ✅ Accordion layout & Select2.

## 👥 Contributors
- **Kementerian Agama Kabupaten Jombang**
- **Tim IT KIPAS** (Kinerja Pengawas)

---
**Made with ❤️ for better education supervision.**
