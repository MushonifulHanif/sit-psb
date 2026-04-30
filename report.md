# 📄 LAPORAN AKHIR PENGERJAAN PROYEK: SIT-PSB
**Sistem Informasi Terpadu - Penerimaan Santri Baru**
*PPRTQ Raudlatul Falah - Versi Final 1.0 (April 2026)*

---

## 1. PENDAHULUAN
Proyek **SIT-PSB** adalah platform digital manajemen pendaftaran santri baru yang dirancang untuk mengotomatisasi seluruh siklus admisi, mulai dari pendaftaran akun, verifikasi keuangan, ujian seleksi online (CBT), hingga pendataan akhir santri diterima. Fokus utama pengembangan adalah pada **Aesthetis Premium**, **Kecepatan Akses**, dan **Kemudahan Penggunaan** di perangkat mobile.

---

## 2. ARSITEKTUR TEKNOLOGI (Tech Stack)
Sistem ini menggunakan arsitektur **Custom MVC-Lite** berbasis PHP Native untuk menjamin performa maksimal tanpa overhead framework berat.

### A. Backend Logic
- **PHP 8.2+:** Pemanfaatan *Typed Properties* dan *Constructor Promotion*.
- **PDO (PHP Data Objects):** Abstraksi database dengan *Prepared Statements* untuk proteksi total dari SQL Injection.
- **Routing Engine:** Sistem routing dinamis di `router.php` yang mendukung URL ramah SEO.
- **Middleware System:** Filter keamanan berlapis untuk otentikasi sesi dan otorisasi hak akses (Role).

### B. Frontend Architecture (Premium UI)
- **Tailwind CSS:** Framework utama untuk desain interface yang modern dan responsif.
- **Alpine.js:** Micro-framework untuk logika UI reaktif (state management tanpa reload).
- **Web Push API:** Integrasi notifikasi push native ke perangkat Android/HP.
- **Library Pendukung:**
  - **SweetAlert2:** Notifikasi interaktif dan dialog konfirmasi premium.
  - **Flatpickr:** Pemilih tanggal dengan kustomisasi tema Emerald.
  - **Quill Editor:** Editor teks kaya (WYSIWYG) untuk manajemen konten landing page.
  - **Font System:** Menggunakan *Instrument Sans* untuk nuansa modern dan *Amiri* untuk teks Arab.

---

## 3. STRUKTUR FOLDER & FILE (Lengkap)
Penyusunan file mengikuti standar modularitas tinggi untuk kemudahan pemeliharaan:
```text
/tespsbpprtq
├── assets/                       # Aset Statis Website
│   ├── css/                      # Custom CSS & Tailwind Output
│   ├── img/                      # Logo, Favicon, & Icon PWA (PNG)
│   │   ├── favicon.png
│   │   ├── icon-192.png
│   │   ├── icon-512.png
│   │   └── logo.png
│   └── js/                       # Custom Scripts & Push Logic
├── classes/                      # Core Logic & Utility Classes
│   ├── Auth.php                  # Manajemen Sesi & Hak Akses
│   ├── Database.php              # Koneksi PDO (Singleton)
│   ├── FileUpload.php            # Engine Penanganan Upload
│   └── Validator.php             # Validasi Input & Keamanan
├── config/                       # Konfigurasi Global
│   ├── app.php                   # Global Helpers & CSRF
│   ├── database.php              # Kredensial Database
│   ├── init.php                  # Inisialisasi Sistem & Sesi
│   └── vapid.php                 # Kunci Keamanan Web Push
├── controllers/                  # Controller (Logika Bisnis)
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── BendaharaDUController.php
│   ├── BendaharaRegController.php
│   ├── LandingController.php
│   ├── MufatisController.php
│   ├── NotificationController.php
│   ├── SantriController.php
│   └── SekretarisController.php
├── exports/                      # Folder Output PDF/Excel
├── middleware/                   # Layer Keamanan (Route Guards)
│   ├── auth.php                  # Guard Otentikasi
│   └── role.php                  # Guard Otorisasi Role
├── migrations/                   # Script Database & Skema
│   ├── migrate.php               # Skema Tabel Utama
│   └── migrate_landing_sections.php # Skema Landing Builder
├── uploads/                      # Penyimpanan Berkas User
├── vendor/                       # Library Pihak Ketiga (Composer)
├── views/                        # Layer Tampilan (UI)
│   ├── admin/                    # Menu & Pengaturan Admin
│   ├── auth/                     # Halaman Login & Register
│   ├── bendahara_du/             # Dashboard Bendahara DU
│   ├── bendahara_reg/            # Dashboard Bendahara Reg
│   ├── cetak/                    # Template Kartu & Kuitansi
│   ├── components/               # Komponen UI (Navbar, Sidebar)
│   ├── errors/                   # Halaman 404 & 403
│   ├── landing/                  # Konten Landing Page Builder
│   ├── layouts/                  # Template Induk (Master)
│   ├── mufatis/                  # Dashboard Penguji
│   ├── santri/                   # Dashboard & Alur Santri
│   └── sekretaris/               # Dashboard Sekretaris
├── .gitignore                    # Daftar Abaikan Git
├── .htaccess                     # URL Rewriting Engine
├── composer.json                 # Definisi Dependensi
├── composer.lock                 # Kunci Versi Dependensi
├── index.php                     # Entry Point Utama
├── manifest.json                 # Konfigurasi Instalasi PWA
├── router.php                    # Sistem Routing URL
└── service-worker.js             # Background Push Notif Logic
```

---

## 4. MANAJEMEN PENGGUNA (User Roles)
Sistem mendukung 6 tingkatan akses dengan wewenang yang berbeda:
1. **Administrator:** Kendali penuh sistem, pengaturan tahun ajaran, dan builder landing page.
2. **Sekretaris:** Manajemen data pendaftar, rekapitulasi, dan ekspor laporan.
3. **Bendahara Reg:** Khusus verifikasi bukti transfer uang pendaftaran awal.
4. **Bendahara DU:** Verifikasi uang pangkal/daftar ulang dan tracking piutang.
5. **Mufatis:** Tim penguji untuk input nilai ujian lisan, tahfidz, dan wawancara.
6. **Santri:** Pengguna utama (pendaftar) yang mengikuti alur pendaftaran.

---

## 5. ALUR KERJA SISTEM (User Flow)
Alur pendaftaran dirancang secara sekuensial (Step-by-Step):
1. **Registrasi:** Santri membuat akun dan membayar biaya pendaftaran.
2. **Verifikasi Keuangan:** Bendahara Reg memvalidasi bukti bayar.
3. **Biodata & Berkas:** Santri melengkapi formulir profil dan upload dokumen.
4. **Seleksi (CBT & Lisan):**
   - **CBT:** Ujian tertulis otomatis di dashboard dengan timer.
   - **Lisan:** Pengambilan nilai oleh Mufatis secara tatap muka.
5. **Pengumuman:** Status kelulusan muncul otomatis berdasarkan hasil akumulasi nilai.
6. **Daftar Ulang:** Pembayaran biaya masuk dan pendataan ukuran seragam secara digital.
7. **Selesai:** Santri mendapatkan status "Selesai" dan siap mengikuti pendidikan.

---

## 6. FITUR UNGGULAN & INOVASI
- **Premium Design Philosophy:** Penggunaan palet warna Emerald, efek Glassmorphism, dan mikro-animasi pada setiap transisi.
- **Smart Push Notification:** Notifikasi langsung ke bar status HP santri/panitia saat ada update status (Bayar diterima, Pengumuman, dll).
- **Annual Year Reset:** Sistem reset cerdas yang memindahkan data tahun lama ke tabel arsip JSON sebelum membersihkan database untuk pendaftaran baru.
- **Dynamic Landing Builder:** Manajemen konten web depan (Hero, Informasi, Syarat, Biaya) secara langsung dari dashboard admin.
- **Progress Tracker:** Visualisasi progres pendaftaran santri menggunakan sistem tab yang interaktif.

---

## 7. KEAMANAN & OPTIMASI
- **CSRF Protection:** Setiap pengiriman data dilindungi token unik.
- **XSS Prevention:** Sanitasi ketat pada seluruh input user.
- **PWA (Progressive Web App):** Web dapat diinstal di HP layaknya aplikasi native melalui `manifest.json`.
- **Database Indexing:** Optimasi query pada tabel pendaftaran yang memiliki volume data besar.

---

## 8. PENUTUP
Sistem **SIT-PSB** ini bukan sekadar alat pendaftaran, melainkan representasi wajah digital **PPRTQ Raudlatul Falah**. Dengan teknologi yang kokoh dan desain yang premium, sistem ini siap mendukung operasional pesantren secara profesional dan modern.

---
**Pati, April 2026**
*Mushoniful Hanif*
