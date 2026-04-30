# 🛡️ Standar Minimal Keamanan Aplikasi (Security by Design)

Dokumen ini adalah *checklist* dan panduan fondasi keamanan yang wajib diterapkan sejak awal pembuatan aplikasi (Web maupun Android). Menerapkan "Security by Design" akan menghemat banyak waktu dan biaya dibandingkan memperbaiki celah setelah aplikasi diserang.

---

## 1. 🔐 Keamanan Akun & Autentikasi
*Fondasi perlindungan identitas pengguna.*

- [ ] **Jangan Pernah Simpan Password Mentah (Plaintext)**: Selalu gunakan algoritma hashing satu arah yang lambat (seperti `bcrypt` atau `Argon2`). Jangan gunakan MD5 atau SHA-1.
- [ ] **Terapkan Rate Limiting (Anti-Brute Force)**: Blokir alamat IP atau akun sementara waktu (misal 15 menit) setelah 5x percobaan login yang salah beruntun.
- [ ] **Gunakan Token/Session yang Aman**:
  - Untuk Web: Gunakan Session ID yang panjang, acak, dan terenkripsi. Set cookie dengan flag `HttpOnly`, `Secure`, dan `SameSite=Lax/Strict`.
  - Untuk API/Android: Gunakan JWT (JSON Web Tokens) dengan *expiration time* yang pendek dan sistem *Refresh Token*.
- [ ] **Pencegahan Session Fixation**: Selalu jalankan `session_regenerate_id(true)` setiap kali user berhasil login atau logout.

## 2. 🗃️ Keamanan Database (Anti-Injection & Kebocoran)
*Melindungi nyawa dari aplikasi, yaitu data.*

- [ ] **Wajib Gunakan Prepared Statements (PDO/MySQLi)**: Jangan pernah menyambungkan variabel/input user langsung ke dalam string query SQL. Ini mencegah 99% serangan **SQL Injection**.
- [ ] **Prinsip Least Privilege**: User database untuk aplikasi jangan menggunakan `root`. Buatkan user khusus yang hanya punya hak `SELECT, INSERT, UPDATE, DELETE` pada database terkait saja.
- [ ] **Jangan Expose File Konfigurasi**: Pastikan file seperti `.env`, `.sql`, `config.php` tidak bisa diakses langsung melalui URL browser (lindungi dengan `.htaccess` atau taruh di luar *public directory*).

## 3. 📝 Validasi & Sanitasi Input (Anti-XSS & Manipulasi)
*Aturan Emas: "Jangan Pernah Percaya Input User"*

- [ ] **Validasi di Dua Sisi (Frontend & Backend)**: Validasi form di HTML/JS saja tidak cukup karena bisa di-*bypass*. Selalu lakukan validasi ulang di sisi PHP/Backend.
- [ ] **Sanitasi Output HTML (Cegah XSS)**: Saat menampilkan data teks dari database ke layar HTML, selalu gunakan fungsi *escape* seperti `htmlspecialchars()`. Jika menggunakan WYSIWYG (teks tebal/miring), gunakan fungsi *sanitizer* (seperti HTMLPurifier atau minimal `strip_tags()` terukur) untuk membuang tag `<script>`.
- [ ] **Cegah IDOR (Insecure Direct Object Reference)**: Saat user ingin mengedit profil atau menghapus data (contoh: `edit.php?id=5`), selalu cek di backend apakah ID = 5 itu benar-benar milik user yang sedang login.

## 4. 📁 Keamanan File Upload
*Mencegah hacker mengunggah virus atau backdoor.*

- [ ] **Validasi MIME Type, Bukan Ekstensi**: Jangan tertipu dengan nama file `foto.jpg`. Periksa isi filenya menggunakan fungsi seperti `finfo_file()` di PHP.
- [ ] **Ganti Nama File (Rename)**: Jangan gunakan nama asli dari user. Ganti dengan nama acak (contoh: `uniqid()`) untuk menghindari eksekusi file ganda atau tebakan URL.
- [ ] **Matikan Eksekusi Script di Folder Upload**: Tambahkan `.htaccess` (berisi `Require all denied` untuk `*.php`) di dalam folder tujuan upload agar file yang lolos tidak bisa dijalankan sebagai program.

## 5. 🌐 Keamanan Server & Jaringan (HTTP Headers)
*Menginstruksikan browser pengunjung untuk memperketat keamanan.*

- [ ] **Wajib HTTPS (SSL/TLS)**: Jangan pernah biarkan halaman login atau transaksi berjalan di atas HTTP biasa.
- [ ] **Pasang HTTP Security Headers** (via `.htaccess` atau Nginx config):
  - `Strict-Transport-Security (HSTS)`: Memaksa browser selalu pakai HTTPS.
  - `X-Frame-Options: SAMEORIGIN`: Mencegah web ditempel di *iframe* penipu (Clickjacking).
  - `X-Content-Type-Options: nosniff`: Memaksa browser mengikuti MIME type asli.
  - `Content-Security-Policy (CSP)`: (Tingkat lanjut) Membatasi CDN atau asal script luar yang boleh jalan di web.
- [ ] **Cegah CSRF (Cross-Site Request Forgery)**: Sertakan token acak (`csrf_token`) yang unik pada setiap form POST (seperti Edit Profil, Transfer, dll) dan verifikasi di backend.

---

## 📱 Tambahan Khusus untuk Aplikasi Mobile (Android/iOS)
*Karena aplikasi terinstall di HP user, kodenya bisa dibongkar.*

- [ ] **Certificate/SSL Pinning**: Pastikan aplikasi Android hanya mau terkoneksi ke API web yang sertifikat SSL-nya cocok. Ini mencegah aplikasi disadap lewat koneksi Wi-Fi publik (Serangan *Man-in-the-Middle*).
- [ ] **Obfuscation (ProGuard/R8)**: Acak kode sumber aplikasi (Java/Kotlin) sebelum di-build menjadi APK. Ini menyulitkan hacker yang mencoba me-*reverse engineering* (membongkar APK) untuk mencari celah.
- [ ] **Jangan Simpan Data Sensitif di Shared Preferences Mentah**: Jika harus menyimpan API Key atau Token di HP, gunakan fitur penyimpanan terenkripsi bawaan OS (contoh: `EncryptedSharedPreferences` di Android atau `Keychain` di iOS).
- [ ] **Root/Jailbreak Detection (Opsional)**: Untuk aplikasi yang sangat sensitif (seperti ujian CBT murni atau keuangan), tambahkan script untuk menolak berjalan di HP yang sudah di-*root*.

---

*Dokumen ini adalah fondasi yang hidup. Selalu perbarui standar ini seiring dengan perkembangan teknologi dan tren serangan cyber terbaru.*
