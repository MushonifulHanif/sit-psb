# SIT-PSB (Sistem Informasi Terpadu - Penerimaan Santri Baru)

Aplikasi web untuk manajemen penerimaan santri baru dengan fitur CBT (Computer Based Test) terintegrasi, manajemen biodata, dan pembayaran.

## Fitur Utama
- **Dashboard Admin & Panitia**: Monitoring pendaftaran, verifikasi pembayaran, dan statistik.
- **Portal Santri**: Pengisian biodata, upload berkas, dan pengerjaan soal CBT.
- **Ujian CBT**: Tes Pilihan Ganda dan Tes Lisan (Hafalan) dengan integrasi audio recording.
- **PWA (Progressive Web App)**: Bisa diinstal di HP dan mendukung notifikasi (Web Push).
- **Notifikasi Terintegrasi**: Menggunakan sistem notifikasi internal dan Push Notification.
- **Optimasi Mobile**: Desain responsif menggunakan Tailwind CSS.

## Preview Tampilan

### Halaman Publik
<p align="center">
  <img src="screenshots/landing_page.png" width="800" alt="Landing Page">
</p>
<p align="center">
  <img src="screenshots/login_page.png" width="400" alt="Login Page">
  <img src="screenshots/registration_page.png" width="400" alt="Registration Page">
</p>

### Panel Admin & Panitia
<p align="center">
  <img src="screenshots/admin_desktop.png" width="400" alt="Admin Dashboard">
  <img src="screenshots/admin_mobile.png" width="200" alt="Admin Mobile">
</p>
<p align="center">
  <img src="screenshots/sekretaris_desktop.png" width="400" alt="Sekretaris Dashboard">
  <img src="screenshots/bendahara_desktop.jpg" width="400" alt="Bendahara Dashboard">
</p>

### Portal Santri & Ujian
<p align="center">
  <img src="screenshots/santri_ujian_desktop.png" width="400" alt="CBT Exam">
  <img src="screenshots/sekretaris_set_ujian_desktop.jpg" width="400" alt="Set Ujian">
</p>

## Teknologi yang Digunakan
- **Backend**: PHP (MVC Architecture)
- **Database**: MySQL (PDO)
- **Frontend**: Tailwind CSS, Alpine.js, SweetAlert2
- **Integrasi**: API e-Quran (untuk tes hafalan)

## Instalasi
1. Clone repositori ini.
2. Import database `db_sit_psb.sql` (jika tersedia secara terpisah).
3. Sesuaikan konfigurasi database di `config/database.php`.
4. Jalankan `composer install` untuk menginstal dependensi.
5. Akses melalui web server (seperti Laragon atau Apache).

## Keamanan
Proyek ini telah melalui audit keamanan dasar, termasuk:
- Proteksi SQL Injection (Prepared Statements).
- Proteksi CSRF.
- Sanitasi Input (XSS Prevention).
- Rate Limiting pada sistem login.
- Security Headers pada `.htaccess`.

---
*Proyek ini dikembangkan untuk kebutuhan internal Pondok Pesantren.*
