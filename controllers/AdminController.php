<?php

class AdminController {

    public function __construct() {
        require_role('admin');
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        
        $pendaftar_count = $db->query("SELECT COUNT(*) FROM users WHERE role='santri'")->fetchColumn();
        $lulus_count = $db->query("SELECT COUNT(*) FROM users WHERE role='santri' AND status_psb IN ('lulus', 'daftar_ulang', 'selesai')")->fetchColumn();
        
        $total_reg = $db->query("SELECT COALESCE(SUM(nominal_bayar), 0) FROM pembayaran WHERE jenis='registrasi' AND status='diterima'")->fetchColumn();
        $total_du = $db->query("SELECT COALESCE(SUM(nominal_bayar), 0) FROM pembayaran WHERE jenis='daftar_ulang' AND status='diterima'")->fetchColumn();
        
        require __DIR__ . '/../views/admin/index.php';
    }

    // --- MANAJEMEN USERS / PANITIA ---
    public function users() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM users WHERE role != 'santri' ORDER BY role ASC");
        $users = $stmt->fetchAll();
        require __DIR__ . '/../views/admin/users.php';
    }

    public function store_user() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/users');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        
        // Simple validation
        $stmtCheck = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmtCheck->execute([$_POST['username']]);
        if ($stmtCheck->fetch()) {
            set_flash_message('error', 'Username sudah dipakai.');
            redirect('admin/users');
        }

        $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmtInsert = $db->prepare("INSERT INTO users (name, username, password, role, no_wa) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->execute([$_POST['name'], $_POST['username'], $passwordHash, $_POST['role'], $_POST['no_wa']]);
        
        set_flash_message('success', 'Panitia berhasil ditambahkan.');
        redirect('admin/users');
    }

    public function update_user($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/users');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        
        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $role = $_POST['role'] ?? '';
        $no_wa = $_POST['no_wa'] ?? null;
        $password = $_POST['password'] ?? '';

        // Check unique username
        $stmtCheck = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmtCheck->execute([$username, $id]);
        if ($stmtCheck->fetch()) {
            set_flash_message('error', 'Username sudah dipakai oleh pengguna lain.');
            redirect('admin/users');
        }

        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmtUpdate = $db->prepare("UPDATE users SET name = ?, username = ?, role = ?, no_wa = ?, password = ? WHERE id = ?");
            $stmtUpdate->execute([$name, $username, $role, $no_wa, $passwordHash, $id]);
        } else {
            $stmtUpdate = $db->prepare("UPDATE users SET name = ?, username = ?, role = ?, no_wa = ? WHERE id = ?");
            $stmtUpdate->execute([$name, $username, $role, $no_wa, $id]);
        }
        
        set_flash_message('success', 'Data panitia berhasil diperbarui.');
        redirect('admin/users');
    }

    public function delete_user($id) {
        $db = Database::getInstance()->getConnection();
        if ($id == Auth::user()['id']) {
            set_flash_message('error', 'Tidak bisa menghapus akun sendiri.');
        } else {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            set_flash_message('success', 'Akun panitia dihapus.');
        }
        redirect('admin/users');
    }

    // --- MANAJEMEN GELOMBANG ---
    public function gelombang() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM gelombang ORDER BY id DESC");
        $gelombang = $stmt->fetchAll();
        require __DIR__ . '/../views/admin/gelombang.php';
    }

    public function store_gelombang() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/gelombang');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        $stmtInsert = $db->prepare("INSERT INTO gelombang (nama, tgl_buka, tgl_tutup, jadwal_ujian_mulai, jadwal_ujian_selesai) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->execute([$_POST['nama'], $_POST['tgl_buka'], $_POST['tgl_tutup'], $_POST['jadwal_ujian_mulai'], $_POST['jadwal_ujian_selesai']]);
        
        set_flash_message('success', 'Gelombang pendaftaran berhasil ditambahkan.');
        redirect('admin/gelombang');
    }

    public function toggle_gelombang($id) {
        $db = Database::getInstance()->getConnection();
        
        // Nonaktifkan semua dulu
        $db->exec("UPDATE gelombang SET is_active = 0");
        
        // Aktifkan yang dipilih
        $stmt = $db->prepare("UPDATE gelombang SET is_active = 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        set_flash_message('success', 'Gelombang aktif diubah.');
        redirect('admin/gelombang');
    }

    public function delete_gelombang($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM gelombang WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Gelombang dihapus.');
        redirect('admin/gelombang');
    }

    // --- MANAJEMEN BANK SOAL ---
    public function soal() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT s.*, g.nama as nama_gel 
            FROM soal_bank s 
            LEFT JOIN gelombang g ON s.gelombang_id = g.id 
            ORDER BY s.gelombang_id DESC, s.urutan ASC
        ");
        $soal = $stmt->fetchAll();
        
        $gelombang = $db->query("SELECT * FROM gelombang ORDER BY id DESC")->fetchAll();

        require __DIR__ . '/../views/admin/soal.php';
    }

    public function store_soal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/soal');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        
        $tipe = $_POST['tipe'];
        $pilihan_json = null;
        $jawaban_benar = null;

        if ($tipe == 'pg') {
            $pilihan = [
                'A' => $_POST['pilihan_A'],
                'B' => $_POST['pilihan_B'],
                'C' => $_POST['pilihan_C'],
                'D' => $_POST['pilihan_D']
            ];
            $pilihan_json = json_encode($pilihan);
            $jawaban_benar = $_POST['jawaban_benar'];
        } elseif ($tipe == 'rekam_suara') {
            if (isset($_POST['is_quran']) && $_POST['is_quran'] == '1') {
                $pilihan = [
                    'is_quran' => true,
                    'surah_no' => $_POST['surah_no'],
                    'surah_name' => $_POST['surah_name'],
                    'ayat_start' => $_POST['ayat_start'],
                    'ayat_end' => $_POST['ayat_end']
                ];
                $pilihan_json = json_encode($pilihan);
            }
        }

        $stmtInsert = $db->prepare("INSERT INTO soal_bank (gelombang_id, tipe, pertanyaan, pilihan_json, jawaban_benar, urutan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtInsert->execute([$_POST['gelombang_id'], $tipe, $_POST['pertanyaan'], $pilihan_json, $jawaban_benar, $_POST['urutan'] ?? 0]);
        
        set_flash_message('success', 'Soal berhasil ditambahkan.');
        redirect('admin/soal');
    }

    public function update_soal($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/soal');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        
        $tipe = $_POST['tipe'];
        $pilihan_json = null;
        $jawaban_benar = null;

        if ($tipe == 'pg') {
            $pilihan = [
                'A' => $_POST['pilihan_A'],
                'B' => $_POST['pilihan_B'],
                'C' => $_POST['pilihan_C'],
                'D' => $_POST['pilihan_D']
            ];
            $pilihan_json = json_encode($pilihan);
            $jawaban_benar = $_POST['jawaban_benar'];
        } elseif ($tipe == 'rekam_suara') {
            if (isset($_POST['is_quran']) && $_POST['is_quran'] == '1') {
                $pilihan = [
                    'is_quran' => true,
                    'surah_no' => $_POST['surah_no'],
                    'surah_name' => $_POST['surah_name'],
                    'ayat_start' => $_POST['ayat_start'],
                    'ayat_end' => $_POST['ayat_end']
                ];
                $pilihan_json = json_encode($pilihan);
            }
        }

        $stmtUpdate = $db->prepare("UPDATE soal_bank SET gelombang_id = ?, tipe = ?, pertanyaan = ?, pilihan_json = ?, jawaban_benar = ?, urutan = ? WHERE id = ?");
        $stmtUpdate->execute([$_POST['gelombang_id'], $tipe, $_POST['pertanyaan'], $pilihan_json, $jawaban_benar, $_POST['urutan'] ?? 0, $id]);
        
        set_flash_message('success', 'Soal berhasil diperbarui.');
        redirect('admin/soal');
    }

    public function delete_soal($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM soal_bank WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Soal dihapus.');
        redirect('admin/soal');
    }

    // --- MANAJEMEN ITEM SERAGAM ---
    public function item_seragam() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM item_seragam ORDER BY urutan ASC");
        $item_seragam = $stmt->fetchAll();
        require __DIR__ . '/../views/admin/item_seragam.php';
    }

    public function store_item_seragam() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/item-seragam');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        $stmtInsert = $db->prepare("INSERT INTO item_seragam (nama_item, jk, satuan, keterangan, urutan) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->execute([$_POST['nama_item'], $_POST['jk'], $_POST['satuan'], $_POST['keterangan'] ?? '', $_POST['urutan'] ?? 0]);
        
        set_flash_message('success', 'Item seragam berhasil ditambahkan.');
        redirect('admin/item-seragam');
    }

    public function update_item_seragam($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/item-seragam');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        $stmtUpdate = $db->prepare("UPDATE item_seragam SET nama_item = ?, jk = ?, satuan = ?, urutan = ? WHERE id = ?");
        $stmtUpdate->execute([$_POST['nama_item'], $_POST['jk'], $_POST['satuan'], $_POST['urutan'] ?? 0, $id]);
        
        set_flash_message('success', 'Item seragam berhasil diperbarui.');
        redirect('admin/item-seragam');
    }

    public function delete_item_seragam($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM item_seragam WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Item seragam dihapus.');
        redirect('admin/item-seragam');
    }

    // --- PENGATURAN SYSTEM ---
    public function pengaturan() {
        $db = Database::getInstance()->getConnection();
        // Load general settings excluding landing page specific contents to simplify UI
        $stmt = $db->query("SELECT * FROM pengaturan WHERE `key` NOT LIKE 'konten_%' AND `key` NOT LIKE 'hero_%' AND `key` NOT LIKE 'url_tutorial_%'");
        $pengaturan = $stmt->fetchAll();
        require __DIR__ . '/../views/admin/pengaturan.php';
    }

    public function update_pengaturan() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/pengaturan');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        
        // Handle normal settings
        if (isset($_POST['setting'])) {
            foreach ($_POST['setting'] as $key => $val) {
                // Sanitize currency fields (strip dots)
                if ($key === 'biaya_registrasi' || $key === 'biaya_daftar_ulang') {
                    $val = str_replace('.', '', $val);
                }
                
                // Ensure key exists before updating to avoid junk data
                $stmtUpdate = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = ?");
                $stmtUpdate->execute([$val, $key]);
            }
        }

        // Handle list_narahubung specifically
        if (isset($_POST['narahubung'])) {
            $narahubung = [];
            foreach ($_POST['narahubung']['nama'] as $i => $nama) {
                $wa = $_POST['narahubung']['wa'][$i] ?? '';
                if (!empty($nama) && !empty($wa)) {
                    $narahubung[] = ['nama' => $nama, 'wa' => $wa];
                }
            }
            $json = json_encode($narahubung);
            $stmtUpdate = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = 'list_narahubung'");
            $stmtUpdate->execute([$json]);
        }
        
        set_flash_message('success', 'Pengaturan berhasil diperbarui.');
        redirect('admin/pengaturan');
    }

    // --- KONTEN LANDING (DYNAMIC SECTIONS) ---
    private function ensure_landing_sections_table($db) {
        $db->exec("CREATE TABLE IF NOT EXISTS landing_sections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            tag VARCHAR(50) NOT NULL,
            content LONGTEXT,
            type ENUM('text', 'video') DEFAULT 'text',
            video_url VARCHAR(255) NULL,
            order_num INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Automated Migration if empty
        $check = $db->query("SELECT COUNT(*) FROM landing_sections")->fetchColumn();
        if ($check == 0) {
            $stmt = $db->query("SELECT `key`, value FROM pengaturan WHERE `key` IN ('konten_informasi', 'konten_syarat', 'konten_biaya', 'url_tutorial_video')");
            $old = [];
            while ($row = $stmt->fetch()) { $old[$row['key']] = $row['value']; }

            $sections = [
                ['title' => 'Informasi Pendaftaran', 'tag' => 'Informasi', 'content' => $old['konten_informasi'] ?? '', 'type' => 'text', 'video_url' => '', 'order_num' => 1],
                ['title' => 'Syarat Pendaftaran', 'tag' => 'Syarat', 'content' => $old['konten_syarat'] ?? '', 'type' => 'text', 'video_url' => '', 'order_num' => 2],
                ['title' => 'Biaya Pendaftaran', 'tag' => 'Biaya', 'content' => $old['konten_biaya'] ?? '', 'type' => 'text', 'video_url' => '', 'order_num' => 3],
            ];

            if (!empty($old['url_tutorial_video'])) {
                $sections[] = ['title' => 'Tutorial Pendaftaran', 'tag' => 'Tutorial', 'content' => 'Tonton video panduan pendaftaran online kami.', 'type' => 'video', 'video_url' => $old['url_tutorial_video'], 'order_num' => 4];
            }

            $stmtIns = $db->prepare("INSERT INTO landing_sections (title, tag, content, type, video_url, order_num) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($sections as $s) {
                $stmtIns->execute([$s['title'], $s['tag'], $s['content'], $s['type'], $s['video_url'], $s['order_num']]);
            }
        }
    }
    private function ensure_settings_keys($db) {
        $keys = [
            'hero_subjudul', 
            'footer_facebook',
            'hero_badge_text',
            'hero_badge_style',
            'hero_badge_animation',
            'hero_badge_size',
            'hero_stats_json',
            'hero_stats_style',
            'hero_images_json',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'app_logo',
            'app_favicon'
        ];
        foreach ($keys as $key) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM pengaturan WHERE `key` = ?");
            $stmt->execute([$key]);
            if ($stmt->fetchColumn() == 0) {
                $val = '';
                if($key == 'hero_badge_style') $val = 'emerald';
                if($key == 'hero_badge_animation') $val = 'ping';
                if($key == 'hero_badge_size') $val = 'text-xs';
                if($key == 'hero_stats_style') $val = 'emerald';
                if($key == 'hero_stats_json') {
                    $val = json_encode([
                        ['icon' => 'users', 'num' => '1200', 'suffix' => '+', 'label' => 'SANTRI AKTIF'],
                        ['icon' => 'office-building', 'num' => '15', 'suffix' => '', 'label' => 'GEDUNG FASILITAS'],
                        ['icon' => 'book-open', 'num' => '50', 'suffix' => '+', 'label' => 'USTADZ & PENGAJAR'],
                        ['icon' => 'emoji-happy', 'num' => '100', 'suffix' => '%', 'label' => 'KENYAMANAN SANTRI']
                    ]);
                }
                $stmtIns = $db->prepare("INSERT INTO pengaturan (`key`, value) VALUES (?, ?)");
                $stmtIns->execute([$key, $val]);
            }
        }
    }

    public function konten_landing() {
         $db = Database::getInstance()->getConnection();
         $this->ensure_landing_sections_table($db);
         $this->ensure_settings_keys($db);

         // Load Hero, SEO & Static Landing Settings
         $stmt = $db->query("SELECT * FROM pengaturan WHERE `key` LIKE 'hero_%' OR `key` LIKE 'seo_%' OR `key` LIKE 'app_%' OR `key` = 'footer_website' OR `key` = 'footer_email' OR `key` = 'footer_instagram' OR `key` = 'footer_facebook'");
         $pengaturan_data = $stmt->fetchAll();
         $konten = [];
         foreach($pengaturan_data as $k) {
             $konten[$k['key']] = $k['value'];
         }

         // Load Dynamic Sections
         $stmtSections = $db->query("SELECT * FROM landing_sections ORDER BY order_num ASC");
         $sections = $stmtSections->fetchAll();

         require __DIR__ . '/../views/admin/konten_landing.php';
    }

    public function update_konten_landing() {
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/konten-landing');
         verify_csrf_token($_POST['csrf_token'] ?? '');
         
         $db = Database::getInstance()->getConnection();
         if (isset($_POST['konten'])) {
             foreach ($_POST['konten'] as $key => $val) {
                 $stmtUpdate = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = ?");
                 $stmtUpdate->execute([$val, $key]);
             }
         }

         if (!empty($_FILES['hero_gambar_file']['name'])) {
             $uploader = new FileUpload('uploads/landing');
             $res = $uploader->upload('hero_gambar_file');
             if (isset($res['success'])) {
                 $path = 'uploads/landing/' . $res['filename'];
                 $stmtUpdate = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = 'hero_gambar'");
                 $stmtUpdate->execute([$path]);
             }
         }

         // Upload Multiple Hero Images
         if (!empty($_FILES['hero_multi_files']['name'][0])) {
            $uploader = new FileUpload('uploads/landing');
            $current_json = get_pengaturan('hero_images_json') ?: '[]';
            $images = json_decode($current_json, true) ?: [];
            
            $files = $_FILES['hero_multi_files'];
            foreach ($files['name'] as $i => $name) {
                if (empty($name)) continue;
                
                $_FILES['tmp_upload'] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                $res = $uploader->upload('tmp_upload');
                if (isset($res['success'])) {
                    $images[] = 'uploads/landing/' . $res['filename'];
                }
            }
            
            $stmtUpdate = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = 'hero_images_json'");
            $stmtUpdate->execute([json_encode($images)]);
         }

         // Handle App Logo Upload
         if (!empty($_FILES['app_logo_file']['name'])) {
            $uploader = new FileUpload('uploads/branding');
            $res = $uploader->upload('app_logo_file');
            if (isset($res['success'])) {
                $path = 'uploads/branding/' . $res['filename'];
                $stmtUpdate = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = 'app_logo'");
                $stmtUpdate->execute([$path]);
            }
         }

         // Handle App Favicon Upload
         if (!empty($_FILES['app_favicon_file']['name'])) {
            $uploader = new FileUpload('uploads/branding');
            $res = $uploader->upload('app_favicon_file');
            if (isset($res['success'])) {
                $path = 'uploads/branding/' . $res['filename'];
                $stmtUpdate = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = 'app_favicon'");
                $stmtUpdate->execute([$path]);
            }
         }
         
         set_flash_message('success', 'Informasi Hero & Footer berhasil diperbarui.');
         redirect('admin/konten-landing');
    }

    public function store_landing_section() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/konten-landing');
        verify_csrf_token($_POST['csrf_token'] ?? '');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO landing_sections (title, tag, content, type, video_url, order_num) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['tag'],
            $_POST['content'] ?? '',
            $_POST['type'] ?? 'text',
            $_POST['video_url'] ?? '',
            $_POST['order_num'] ?? 0
        ]);

        set_flash_message('success', 'Seksi baru berhasil ditambahkan.');
        redirect('admin/konten-landing');
    }

    public function update_landing_section() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/konten-landing');
        verify_csrf_token($_POST['csrf_token'] ?? '');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE landing_sections SET title = ?, tag = ?, content = ?, type = ?, video_url = ?, order_num = ? WHERE id = ?");
        $stmt->execute([
            $_POST['title'],
            $_POST['tag'],
            $_POST['content'] ?? '',
            $_POST['type'] ?? 'text',
            $_POST['video_url'] ?? '',
            $_POST['order_num'] ?? 0,
            $_POST['id']
        ]);

        set_flash_message('success', 'Seksi berhasil diperbarui.');
        redirect('admin/konten-landing');
    }

    public function toggle_landing_section($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE landing_sections SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Status seksi berhasil diubah.');
        redirect('admin/konten-landing');
    }

    public function delete_landing_section($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM landing_sections WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Seksi berhasil dihapus.');
        redirect('admin/konten-landing');
    }

    public function delete_hero_image() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/konten-landing');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $path = $_POST['path'] ?? '';
        if (empty($path)) redirect('admin/konten-landing');
        
        $db = Database::getInstance()->getConnection();
        $current_json = get_pengaturan('hero_images_json') ?: '[]';
        $images = json_decode($current_json, true) ?: [];
        
        $new_images = array_values(array_filter($images, function($img) use ($path) {
            return $img !== $path;
        }));
        
        $stmtUpdate = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = 'hero_images_json'");
        $stmtUpdate->execute([json_encode($new_images)]);
        
        // Delete file
        if (file_exists(__DIR__ . '/../../' . $path)) {
            unlink(__DIR__ . '/../../' . $path);
        }
        
        set_flash_message('success', 'Gambar berhasil dihapus dari galeri.');
        redirect('admin/konten-landing');
    }

    // --- TAHUN AJARAN & ARCHIVE ---
    public function tahun_ajaran() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM arsip_tahun_ajaran ORDER BY id DESC");
        $arsip = $stmt->fetchAll();
        require __DIR__ . '/../views/admin/tahun_ajaran.php';
    }

    public function proses_reset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/tahun-ajaran');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        set_time_limit(120);

        $db = Database::getInstance()->getConnection();
        $tahun_ajaran_lama = get_pengaturan('tahun_ajaran');
        $tahun_ajaran_baru = $_POST['tahun_ajaran_baru'] ?? '';

        if (empty($tahun_ajaran_baru)) {
            set_flash_message('error', 'Tahun ajaran baru harus diisi.');
            redirect('admin/tahun-ajaran');
        }

        try {
            $db->beginTransaction();

            // 1. Kumpulkan Laporan
            $total_pendaftar = $db->query("SELECT COUNT(*) FROM users WHERE role='santri'")->fetchColumn();
            $total_lulus = $db->query("SELECT COUNT(*) FROM users WHERE role='santri' AND status_psb IN ('lulus','daftar_ulang','selesai')")->fetchColumn();
            $total_gagal = $db->query("SELECT COUNT(*) FROM users WHERE role='santri' AND status_psb = 'gagal'")->fetchColumn();
            $total_du = $db->query("SELECT COUNT(*) FROM users WHERE role='santri' AND status_psb IN ('daftar_ulang','selesai')")->fetchColumn();
            
            $pemasukan_reg = $db->query("SELECT COALESCE(SUM(nominal_bayar), 0) FROM pembayaran WHERE jenis='registrasi' AND status='diterima'")->fetchColumn();
            $pemasukan_du = $db->query("SELECT COALESCE(SUM(nominal_bayar), 0) FROM pembayaran WHERE jenis='daftar_ulang' AND status='diterima'")->fetchColumn();
            $pemasukan_infaq = $db->query("SELECT COALESCE(SUM(nominal_infaq), 0) FROM pembayaran WHERE jenis='daftar_ulang' AND status='diterima'")->fetchColumn();
            
            // Backup Biodata & Pendaftar as JSON
            $stmtBio = $db->query("SELECT u.username, u.name, u.status_psb, b.* FROM users u LEFT JOIN biodata_santri b ON u.id = b.user_id WHERE u.role='santri'");
            $data_json = json_encode($stmtBio->fetchAll(PDO::FETCH_ASSOC));

            // Simpan ke Arsip
            $stmtArsip = $db->prepare("
                INSERT INTO arsip_tahun_ajaran (
                    tahun_ajaran, total_pendaftar, total_lulus, total_gagal, total_daftar_ulang,
                    total_pemasukan_registrasi, total_pemasukan_daftar_ulang, total_pemasukan_infaq,
                    total_pemasukan_keseluruhan, data_json
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtArsip->execute([
                $tahun_ajaran_lama, $total_pendaftar, $total_lulus, $total_gagal, $total_du,
                $pemasukan_reg, $pemasukan_du, $pemasukan_infaq, ($pemasukan_reg + $pemasukan_du + $pemasukan_infaq),
                $data_json
            ]);

            // 2. SET NULL foreign keys (disable referential integrity locally)
            $db->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            $db->exec("TRUNCATE TABLE biodata_santri");
            $db->exec("TRUNCATE TABLE ujian_cbt");
            $db->exec("TRUNCATE TABLE ujian_lisan");
            $db->exec("TRUNCATE TABLE pembayaran");
            $db->exec("TRUNCATE TABLE seragam");
            $db->exec("DELETE FROM push_subscriptions"); // optional
            
            // Hapus users santri saja
            $db->exec("DELETE FROM users WHERE role = 'santri'");
            
            // Hapus gelombang & reset auto increment
            $db->exec("TRUNCATE TABLE gelombang");
            
            // Biarkan soal_bank & item_seragam, tapi opsional jika ingin diclear. 
            // Kita clear referensi soal ke gelombang jika gelombang dihapus, tapi di migration gelombang CASCADE.
            // Karena TRUNCATE gelombang, soal_bank otomatis kosong jika FK Cascade aktif tapi tadi kita matikan FK check.
            $db->exec("TRUNCATE TABLE soal_bank");

            $db->exec("SET FOREIGN_KEY_CHECKS = 1");

            // 3. Update Pengaturan Tahun Ajaran
            $stmtUpdateTA = $db->prepare("UPDATE pengaturan SET value = ? WHERE `key` = 'tahun_ajaran'");
            $stmtUpdateTA->execute([$tahun_ajaran_baru]);

            // Reset ID Counter
            $db->exec("ALTER TABLE users AUTO_INCREMENT = 1");

            $db->commit();
            set_flash_message('success', "Data berhasil di-reset. Tahun ajaran baru ({$tahun_ajaran_baru}) telah diaktifkan.");

        } catch (Exception $e) {
            $db->rollBack();
            set_flash_message('error', 'Gagal mereset data: ' . $e->getMessage());
        }

        redirect('admin/tahun-ajaran');
    }
}
