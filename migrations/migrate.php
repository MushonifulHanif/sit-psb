<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';

$pdo = Database::getInstance()->getConnection();

echo "Memulai migrasi database...\n";

// Disable foreign keys temporarily
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

// Array tabel yang harus dibuat sesuai versi 6
$tables = [
    'arsip_tahun_ajaran' => "
        CREATE TABLE IF NOT EXISTS arsip_tahun_ajaran (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tahun_ajaran VARCHAR(20) NOT NULL,
            total_pendaftar INT DEFAULT 0,
            total_lulus INT DEFAULT 0,
            total_gagal INT DEFAULT 0,
            total_daftar_ulang INT DEFAULT 0,
            total_pemasukan_registrasi DECIMAL(15,2) DEFAULT 0,
            total_pemasukan_daftar_ulang DECIMAL(15,2) DEFAULT 0,
            total_pemasukan_infaq DECIMAL(15,2) DEFAULT 0,
            total_pemasukan_keseluruhan DECIMAL(15,2) DEFAULT 0,
            jumlah_santri_du_lunas INT DEFAULT 0,
            jumlah_santri_du_belum_lunas INT DEFAULT 0,
            data_json LONGTEXT,
            laporan_json LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ",
    'gelombang' => "
        CREATE TABLE IF NOT EXISTS gelombang (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(50) NOT NULL,
            tgl_buka DATE NOT NULL,
            tgl_tutup DATE NOT NULL,
            jadwal_ujian_mulai DATETIME NOT NULL,
            jadwal_ujian_selesai DATETIME NOT NULL,
            is_active TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ",
    'users' => "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin','sekretaris','bendahara_reg','bendahara_du','mufatis','santri') NOT NULL DEFAULT 'santri',
            status_psb ENUM('pendaftar','siap_ujian','sudah_ujian','lulus','gagal','daftar_ulang','selesai') DEFAULT 'pendaftar',
            gelombang_id INT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (gelombang_id) REFERENCES gelombang(id) ON DELETE SET NULL
        );
    ",
    'biodata_santri' => "
        CREATE TABLE IF NOT EXISTS biodata_santri (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            jk ENUM('L','P') NOT NULL,
            tempat_lahir VARCHAR(100),
            tanggal_lahir DATE,
            nik VARCHAR(20),
            asal_sekolah VARCHAR(150),
            no_wa VARCHAR(20) NOT NULL,
            nama_ayah VARCHAR(100),
            pekerjaan_ayah VARCHAR(100),
            nama_ibu VARCHAR(100),
            pekerjaan_ibu VARCHAR(100),
            alamat TEXT,
            foto VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ",
    'soal_bank' => "
        CREATE TABLE IF NOT EXISTS soal_bank (
            id INT AUTO_INCREMENT PRIMARY KEY,
            gelombang_id INT NOT NULL,
            tipe ENUM('pg','rekam_suara') NOT NULL DEFAULT 'pg',
            pertanyaan TEXT NOT NULL,
            pilihan_json JSON NULL,
            jawaban_benar VARCHAR(5) NULL,
            urutan INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (gelombang_id) REFERENCES gelombang(id) ON DELETE CASCADE
        );
    ",
    'ujian_cbt' => "
        CREATE TABLE IF NOT EXISTS ujian_cbt (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            sudah_kirim_video TINYINT(1) DEFAULT 0,
            skor_pg INT DEFAULT 0,
            jawaban_json JSON NULL,
            rekaman_json JSON NULL,
            waktu_mulai DATETIME,
            waktu_submit DATETIME,
            durasi_detik INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ",
    'ujian_lisan' => "
        CREATE TABLE IF NOT EXISTS ujian_lisan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            mufatis_id INT NOT NULL,
            nilai DECIMAL(5,2) DEFAULT 0,
            catatan TEXT,
            status_kelulusan ENUM('belum','lulus','gagal') DEFAULT 'belum',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (mufatis_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ",
    'pembayaran' => "
        CREATE TABLE IF NOT EXISTS pembayaran (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            jenis ENUM('registrasi','daftar_ulang') NOT NULL,
            nominal_bayar DECIMAL(12,2) NOT NULL,
            nominal_infaq DECIMAL(12,2) DEFAULT 0,
            bukti_transfer VARCHAR(255) NOT NULL,
            catatan_santri TEXT,
            status ENUM('pending','diterima','ditolak') DEFAULT 'pending',
            catatan_verifikasi TEXT,
            verified_by INT NULL,
            verified_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
        );
    ",
    'item_seragam' => "
        CREATE TABLE IF NOT EXISTS item_seragam (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_item VARCHAR(100) NOT NULL,
            jk ENUM('L','P','semua') NOT NULL,
            satuan VARCHAR(20) DEFAULT 'cm',
            keterangan TEXT,
            urutan INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ",
    'seragam' => "
        CREATE TABLE IF NOT EXISTS seragam (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            detail_ukuran_json JSON NOT NULL,
            catatan TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ",
    'pengaturan' => "
        CREATE TABLE IF NOT EXISTS pengaturan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(100) NOT NULL UNIQUE,
            value TEXT,
            keterangan VARCHAR(255)
        );
    ",
    'notifikasi' => "
        CREATE TABLE IF NOT EXISTS notifikasi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            target_role VARCHAR(20) NULL,
            target_user_id INT NULL,
            judul VARCHAR(150) NOT NULL,
            pesan TEXT,
            link VARCHAR(255),
            read_by_json JSON DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ",
    'push_subscriptions' => "
        CREATE TABLE IF NOT EXISTS push_subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            endpoint TEXT NOT NULL,
            p256dh_key VARCHAR(255) NOT NULL,
            auth_key VARCHAR(255) NOT NULL,
            user_agent VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    "
];

foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "Tabel '$name' berhasil dibuat.\n";
    } catch (PDOException $e) {
        echo "Gagal membuat tabel '$name': " . $e->getMessage() . "\n";
    }
}

// Seed default setup
echo "Menyisipkan data awal (seeding)...\n";

try {
    // Admin
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Administrator', 'admin', $password, 'admin']);

    // Mufatis 
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Ustadz Abdullah', 'mufatis1', $password, 'mufatis']);
    
    // Sekretaris
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Sekretaris PSB', 'sekretaris', $password, 'sekretaris']);

    // Bendahara Reg
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Bendahara Registrasi', 'bendahara_reg', $password, 'bendahara_reg']);

    // Bendahara DU
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Ustadzah Fatimah', 'bendahara_du', $password, 'bendahara_du']);


    // Pengaturan Defaults
    $pengaturan_defaults = [
        ['nama_pesantren', 'PPRTQ Raudlatul Falah', 'Nama pondok pesantren'],
        ['alamat_pesantren', 'Jl. KH. Agus Salim No. 123', 'Alamat lengkap'],
        ['no_telp', '081234567890', 'Nomor telepon'],
        ['tahun_ajaran', '2026/2027', 'Tahun ajaran aktif'],
        ['no_rekening_bsi', '1234567890', 'Nomor rekening BSI'],
        ['nama_rekening_bsi', 'PPRTQ', 'Atas nama rekening'],
        ['biaya_registrasi', '200000', 'Biaya registrasi (Rp)'],
        ['biaya_daftar_ulang', '5000000', 'Biaya daftar ulang (Rp)'],
        ['mode_ujian', 'serempak', 'Mode ujian: serempak / on_demand'],
        ['durasi_ujian_menit', '45', 'Durasi ujian CBT (menit)'],
        ['nama_mufatis', 'Ustadz Abdullah', 'Nama Mufatis (penguji)'],
        ['wa_mufatis', '6281234567890', 'No WA Mufatis'],
        ['template_wa_hafalan', 'Assalamu\'alaikum Warahmatullahi Wabarakatuh\n\nAlhamdulillah, saya {nama} dengan nomor tes {no_tes} telah menyelesaikan Tes PSB PPRTQ Raudlatul Falah Tahun Ajaran {tahun_ajaran}.\n\nBersama ini saya kirimkan Video Hafalan Surah Al-Ghasiyah.', 'Template pesan WA hafalan'],
        ['nama_bendahara_du', 'Ustadzah Fatimah', 'Nama Bendahara DU'],
        ['wa_bendahara_du', '6281234567891', 'No WA Bendahara DU'],
        ['pesan_infaq', 'Mengingat Ananda dinyatakan LULUS, kami mengajak Bapak/Ibu menyisihkan sebagian harta untuk Infaq Sukarela (seikhlasnya) sebagai wujud syukur Ananda telah DITERIMA di pesantren kami.', 'Pesan ajakan infaq'],
        ['hero_gambar', '', 'Path gambar hero landing page'],
        ['hero_judul', 'Penerimaan Santri Baru', 'Judul hero'],
        ['hero_subjudul', 'PPRTQ Raudlatul Falah', 'Sub judul hero'],
        ['konten_informasi', 'Informasi waktu dan tempat', 'Konten section Informasi'],
        ['url_tutorial_video', '', 'URL embed tutorial video'],
        ['konten_syarat', '1. Mengisi formulir\n2. Melunasi biaya\n', 'Konten section Syarat'],
        ['konten_biaya', 'Biaya Pendaftaran Rp 200.000', 'Konten section Biaya (HTML)'],
        ['footer_website', 'http://example.com', 'URL website utama'],
        ['footer_email', 'admin@example.com', 'Email pesantren'],
        ['footer_instagram', 'https://instagram.com/pprtq', 'Link Instagram'],
        ['wa_narahubung', '6281234567892', 'No WA narahubung PSB'],
        ['tanggal_masuk_pondok', '2026-06-21', 'Tanggal masuk pondok santri baru'],
        ['pesan_selamat', 'Alhamdulillah, Ananda telah menyelesaikan seluruh proses Penerimaan Santri Baru PPRTQ Raudlatul Falah. Mohon hadir tepat waktu di kampus pesantren dengan membawa perlengkapan yang telah ditentukan.', 'Pesan setelah submit seragam'],
        ['vapid_public_key', '', 'VAPID Public Key'],
        ['vapid_private_key', '', 'VAPID Private Key'],
        ['vapid_email', '', 'Email VAPID']
    ];

    $stmt_pengaturan = $pdo->prepare("INSERT IGNORE INTO pengaturan (`key`, value, keterangan) VALUES (?, ?, ?)");
    foreach ($pengaturan_defaults as $item) {
         $stmt_pengaturan->execute($item);
    }
    
    // Seed Item Seragam Default
    $item_seragam = [
        ['Baju Koko', 'L', 'cm', 'Atasan', 1],
        ['Celana Panjang', 'L', 'cm', 'Bawahan', 2],
        ['Gamis', 'P', 'cm', 'Pakaian', 1],
        ['Kerudung', 'P', 'cm', 'Atasan', 2]
    ];
    $stmt_seragam = $pdo->prepare("INSERT IGNORE INTO item_seragam (nama_item, jk, satuan, keterangan, urutan) VALUES (?, ?, ?, ?, ?)");
    foreach ($item_seragam as $item) {
        $stmt_seragam->execute($item);
    }

    echo "Seeding berhasil.\n";

} catch (PDOException $e) {
    echo "Seeding gagal: " . $e->getMessage() . "\n";
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

echo "Migrasi Selesai.\n";
