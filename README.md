# 📊 KIPAS - Aplikasi Kinerja Pengawas Kemenag Jombang

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

> **Sistem Manajemen Kinerja Pengawas Madrasah** - Aplikasi berbasis web untuk monitoring dan validasi kinerja pengawas Kementerian Agama Kabupaten Jombang dengan fitur verifikasi metadata foto (EXIF).

---

## 📋 Deskripsi

**KIPAS (Kinerja Pengawas)** adalah aplikasi web modern yang dirancang khusus untuk Kementerian Agama Kabupaten Jombang dalam mengelola, memonitor, dan memvalidasi laporan kinerja pengawas madrasah. Aplikasi ini dilengkapi dengan fitur inovatif **verifikasi metadata foto** untuk mencegah manipulasi bukti kegiatan.

### ✨ Fitur Utama

#### 🔐 **Multi-Role Authentication**
- **Admin**: Kelola user, madrasah, dan jenis kegiatan
- **Pimpinan**: Validasi dan approve laporan kinerja
- **Pengawas**: Input dan monitor laporan kinerja

#### 📸 **Verifikasi Metadata Foto (EXIF)**
- Ekstraksi otomatis **timestamp** foto (tanggal & waktu pengambilan)
- Ekstraksi **koordinat GPS** lokasi foto diambil
- **Soft validation** dengan warning system
- Integrasi **Google Maps** untuk verifikasi lokasi
- Deteksi foto yang dimanipulasi atau tanpa metadata

#### 📊 **Dashboard Interaktif**
- Statistik real-time kinerja pengawas
- Grafik visualisasi data
- Card dengan gradient modern
- Responsive untuk semua device

#### 📝 **Manajemen Kinerja**
- Input laporan kinerja dengan bukti fisik
- Upload foto/dokumen (JPG, PNG, PDF)
- Edit laporan sebelum disetujui
- Tracking status (Pending, Disetujui, Ditolak)

#### 🎨 **Modern UI/UX**
- Design premium dengan palet Kemenag (hijau & emas)
- Glassmorphism effect pada login page
- Fully responsive (Mobile-first design)
- Dark mode ready components

---

## 🚀 Teknologi

### Backend
- **PHP Native** (7.4+)
- **MySQLi** untuk database
- **Session-based** authentication
- **EXIF Extension** untuk metadata extraction

### Frontend
- **Bootstrap 5.3** - Framework CSS
- **FontAwesome 6** - Icon library
- **Google Fonts (Inter)** - Typography
- **Vanilla JavaScript** - Interactivity

### Database
- **MySQL 5.7+** / **MariaDB 10.3+**

---

## 📦 Instalasi

### Prerequisites
```bash
- PHP >= 7.4
- MySQL >= 5.7 atau MariaDB >= 10.3
- Apache/Nginx Web Server
- PHP Extensions: mysqli, exif, gd
```

### Langkah Instalasi

1. **Clone Repository**
```bash
git clone https://github.com/achsan490/kipas-kembang.git
cd kipas-kembang
```

2. **Setup Database**
```bash
# Buat database baru
mysql -u root -p -e "CREATE DATABASE kinerja_pengawas"

# Import schema
mysql -u root -p kinerja_pengawas < database.sql

# Import metadata columns (untuk fitur EXIF)
mysql -u root -p kinerja_pengawas < update_metadata_columns.sql
```

3. **Konfigurasi Database**
Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kinerja_pengawas');
```

4. **Setup Permissions**
```bash
# Buat folder uploads dan set permissions
mkdir uploads
chmod 777 uploads
```

5. **Akses Aplikasi**
```
http://localhost/kipas-kembang
```

---

## 👥 Default Credentials

### Admin
- **NIP**: `admin`
- **Password**: `admin123`

### Pimpinan
- **NIP**: `197512122000031002`
- **Password**: `password`

### Pengawas (Contoh)
- **NIP**: `198001012005011001`
- **Password**: `password`

> ⚠️ **PENTING**: Ganti password default setelah instalasi pertama!

---

## 📱 Fitur Verifikasi Metadata Foto

### Cara Kerja

1. **Upload Foto** - Pengawas upload foto bukti kegiatan
2. **Ekstraksi EXIF** - Sistem otomatis ekstrak:
   - Timestamp (tanggal & jam foto diambil)
   - GPS Coordinates (latitude & longitude)
   - Camera info
3. **Validasi** - Sistem validasi timestamp dengan tanggal kegiatan
4. **Display** - Pimpinan dapat melihat metadata di halaman validasi

### Tips untuk Pengawas

✅ **DO:**
- Aktifkan GPS/Lokasi sebelum foto
- Gunakan kamera HP langsung (bukan screenshot)
- Upload segera setelah kegiatan
- Pastikan tanggal/waktu HP akurat

❌ **DON'T:**
- Upload foto lama (> 7 hari)
- Edit foto sebelum upload
- Screenshot foto dari gallery
- Matikan GPS saat foto

### Validasi Rules

| Kondisi | Level | Keterangan |
|---------|-------|------------|
| Foto tanpa EXIF | ⚠️ Warning | Tetap diterima, tapi diberi warning |
| Timestamp > 7 hari | 🔴 Danger | Foto terlalu lama dari tanggal kegiatan |
| Timestamp 3-7 hari | ⚠️ Warning | Perlu verifikasi manual |
| Timestamp < 3 hari | ✅ Success | Valid |
| Tanpa GPS | ℹ️ Info | GPS opsional (direkomendasikan) |

---

## 📂 Struktur Project

```
kipas-kembang/
├── assets/
│   ├── css/
│   │   └── style.css          # Custom styles
│   └── img/
│       ├── favicon.png        # App icon
│       └── logo-kinerja.jpg   # App logo
├── config/
│   └── database.php           # Database config
├── core/
│   ├── koneksi.php           # Database connection
│   ├── auth.php              # Authentication
│   ├── functions.php         # Helper functions
│   └── exif_helper.php       # EXIF extraction functions
├── modules/
│   ├── auth/
│   │   └── login.php         # Login page
│   ├── dashboard/
│   │   └── index.php         # Dashboard
│   ├── kinerja/
│   │   ├── index.php         # List kinerja
│   │   ├── tambah.php        # Add kinerja
│   │   ├── edit.php          # Edit kinerja
│   │   └── aksi.php          # Kinerja actions
│   ├── validasi/
│   │   ├── index.php         # Validation page
│   │   └── aksi.php          # Validation actions
│   └── admin/
│       ├── users/            # User management
│       └── madrasah/         # Madrasah management
├── templates/
│   ├── header.php            # Header template
│   ├── sidebar.php           # Sidebar template
│   └── footer.php            # Footer template
├── uploads/                  # Upload directory
├── database.sql              # Database schema
├── update_metadata_columns.sql # Metadata columns
└── README.md                 # This file
```

---

## 🎯 Roadmap

- [x] Multi-role authentication
- [x] CRUD Kinerja
- [x] Validation workflow
- [x] EXIF metadata extraction
- [x] GPS verification
- [x] Modern UI/UX
- [ ] Export to PDF/Excel
- [ ] Email notifications
- [ ] Mobile app (React Native)
- [ ] API REST
- [ ] Dashboard analytics

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**San Project**
- GitHub: [@achsan490](https://github.com/achsan490)
- Email: [Your Email]

---

## 🙏 Acknowledgments

- Kementerian Agama Kabupaten Jombang
- Tim IT Kemenag Jombang
- Bootstrap Team
- FontAwesome Team

---

## 📞 Support

Jika ada pertanyaan atau butuh bantuan:
- 📧 Email: [Your Email]
- 🐛 Issues: [GitHub Issues](https://github.com/achsan490/kipas-kembang/issues)

---

<div align="center">
  <p>Made with ❤️ by San Project</p>
  <p>© 2026 San Project. All rights reserved.</p>
</div>
