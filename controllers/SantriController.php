<?php

class SantriController {
    
    public function __construct() {
        require_auth();
        if (!Auth::isSantri()) {
            redirect(str_replace('_', '-', Auth::user()['role']));
        }
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        $user_id = Auth::user()['id'];
        
        // Load User Details
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        // Load Gelombang
        $gelombang = null;
        if ($user['gelombang_id']) {
            $stmtGel = $db->prepare("SELECT * FROM gelombang WHERE id = ?");
            $stmtGel->execute([$user['gelombang_id']]);
            $gelombang = $stmtGel->fetch();
        } else {
            // fallback, find active gelombang
            $stmtGel = $db->query("SELECT * FROM gelombang WHERE is_active = 1 LIMIT 1");
            $gelombang = $stmtGel->fetch();
        }

        // Load Pengaturan
        $stmtPengaturan = $db->query("SELECT `key`, value FROM pengaturan");
        $settings = [];
        while ($row = $stmtPengaturan->fetch()) {
            $settings[$row['key']] = $row['value'];
        }

        // Load Biodata
        $stmtBio = $db->prepare("SELECT * FROM biodata_santri WHERE user_id = ?");
        $stmtBio->execute([$user_id]);
        $biodata = $stmtBio->fetch();

        // Load CBT
        $stmtCbt = $db->prepare("SELECT * FROM ujian_cbt WHERE user_id = ?");
        $stmtCbt->execute([$user_id]);
        $cbt = $stmtCbt->fetch();

        // Load Mufatis Info (Priority: Assigned -> First Available)
        if (!empty($user['mufatis_id'])) {
            $stmtMufatis = $db->prepare("SELECT name, no_wa FROM users WHERE id = ? AND role = 'mufatis'");
            $stmtMufatis->execute([$user['mufatis_id']]);
            $mufatis_info = $stmtMufatis->fetch();
        }
        
        if (empty($mufatis_info)) {
            $stmtMufatis = $db->prepare("SELECT name, no_wa FROM users WHERE role = 'mufatis' ORDER BY id ASC LIMIT 1");
            $stmtMufatis->execute();
            $mufatis_info = $stmtMufatis->fetch();
        }

        // Load Bendahara DU Info (Dynamic)
        $stmtBendaharaDU = $db->prepare("SELECT name, no_wa FROM users WHERE role = 'bendahara_du' ORDER BY id ASC LIMIT 1");
        $stmtBendaharaDU->execute();
        $bendahara_du_info = $stmtBendaharaDU->fetch();

        // Load Pembayaran Registrasi & DU
        $stmtPembayaran = $db->prepare("SELECT * FROM pembayaran WHERE user_id = ? ORDER BY id DESC");
        $stmtPembayaran->execute([$user_id]);
        $pembayaran_list = $stmtPembayaran->fetchAll();

        $total_bayar_reg = 0;
        $total_bayar_du = 0;
        $total_infaq = 0;
        $riwayat_du = [];

        foreach ($pembayaran_list as $p) {
            if ($p['jenis'] == 'registrasi' && $p['status'] == 'diterima') {
                $total_bayar_reg += $p['nominal_bayar'];
            } elseif ($p['jenis'] == 'daftar_ulang') {
                $riwayat_du[] = $p;
                if ($p['status'] == 'diterima') {
                    $total_bayar_du += $p['nominal_bayar'];
                    $total_infaq += $p['nominal_infaq'];
                }
            }
        }
        
        // Seragam
        $stmtSeragam = $db->prepare("SELECT * FROM seragam WHERE user_id = ?");
        $stmtSeragam->execute([$user_id]);
        $seragam_sudah = $stmtSeragam->fetch();
        
        // Item Seragam Master (Filtered by Gender)
        $jk = $biodata['jk'] ?? 'L';
        $stmtItems = $db->prepare("SELECT * FROM item_seragam WHERE is_active = 1 AND (jk = ? OR jk = 'semua') ORDER BY urutan ASC");
        $stmtItems->execute([$jk]);
        $item_seragam_list = $stmtItems->fetchAll();

        // New Logic for Workflow Gates (Option B)
        // 1. Check if Registration Payment is verified
        $pembayaran_aktif = false;
        foreach($pembayaran_list as $p) {
            if($p['jenis'] == 'registrasi' && $p['status'] == 'diterima') {
                $pembayaran_aktif = true;
                break;
            }
        }
        
        $can_fill_biodata = $pembayaran_aktif;
        $is_biodata_lengkap = ($biodata['is_lengkap'] ?? 0) == 1;
        $can_start_cbt = $is_biodata_lengkap;

        require __DIR__ . '/../views/santri/dashboard.php';
    }

    public function simpan_biodata() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('santri');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        $db = Database::getInstance()->getConnection();
        $user_id = Auth::user()['id'];

        // Initial check: Payment must be verified
        $stmtPay = $db->prepare("SELECT id FROM pembayaran WHERE user_id = ? AND jenis = 'registrasi' AND status = 'diterima'");
        $stmtPay->execute([$user_id]);
        if (!$stmtPay->fetch()) {
            set_flash_message('error', 'Pembayaran registrasi belum diverifikasi. Silakan bayar terlebih dahulu.');
            redirect('santri');
        }

        // Validation (All required except NISN)
        $v = new Validator();
        $v->required($_POST, [
            'jenjang', 'asal_sekolah', 'name', 'nama_panggilan', 'jk', 
            'tempat_lahir', 'tgl_lahir', 'kewarganegaraan', 'jumlah_saudara', 
            'alamat_lengkap', 'nama_ayah', 'nama_ibu', 'wa_ortu'
        ]);

        if ($v->hasErrors()) {
            set_flash_message('error', 'Mohon lengkapi seluruh data wajib (Bertanda *). ' . implode(', ', $v->getErrors()));
            redirect('santri');
        }

        // Handling Files (Organized by Student Folder)
        $user_data = Auth::user();
        $safe_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $_POST['name']);
        $student_folder = $user_data['username'] . '_' . $safe_name . '_File';
        $folder_path = 'uploads/berkas_santri/' . $student_folder;
        
        $uploader = new FileUpload($folder_path);
        $file_fields = ['file_akta', 'file_ktp', 'file_kk', 'file_foto'];
        $file_paths = [];
 
        // Check if existing files exist to allow partial update
        $stmtExist = $db->prepare("SELECT file_akta, file_ktp, file_kk, file_foto FROM biodata_santri WHERE user_id = ?");
        $stmtExist->execute([$user_id]);
        $existing = $stmtExist->fetch();
 
        foreach ($file_fields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $res = $uploader->upload($field);
                if (isset($res['error'])) {
                    set_flash_message('error', "Gagal upload {$field}: " . $res['error']);
                    redirect('santri');
                }
                $file_paths[$field] = $folder_path . '/' . $res['filename'];
            } else {
                // If not uploaded, must exist in DB (if already complete)
                if (empty($existing[$field])) {
                    set_flash_message('error', "Dokumen " . str_replace('file_', '', $field) . " wajib diunggah.");
                    redirect('santri');
                }
                $file_paths[$field] = $existing[$field];
            }
        }

        try {
            $db->beginTransaction();

            // Update Name in users table (Confirming/Editing)
            $stmtUser = $db->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmtUser->execute([$_POST['name'], $user_id]);

            // Update Biodata
            $stmtBio = $db->prepare("
                UPDATE biodata_santri SET 
                    jenjang = ?, asal_sekolah = ?, nama_panggilan = ?, nisn = ?, 
                    tempat_lahir = ?, tgl_lahir = ?, kewarganegaraan = ?, jumlah_saudara = ?, 
                    alamat_lengkap = ?, nama_ayah = ?, nama_ibu = ?, wa_ortu = ?,
                    file_akta = ?, file_ktp = ?, file_kk = ?, file_foto = ?,
                    is_lengkap = 1
                WHERE user_id = ?
            ");
            $wa_ortu_clean = preg_replace('/[^0-9]/', '', $_POST['wa_ortu'] ?? '');
            $stmtBio->execute([
                $_POST['jenjang'], $_POST['asal_sekolah'], $_POST['nama_panggilan'], $_POST['nisn'] ?? null,
                $_POST['tempat_lahir'], $_POST['tgl_lahir'], $_POST['kewarganegaraan'], $_POST['jumlah_saudara'],
                $_POST['alamat_lengkap'], $_POST['nama_ayah'], $_POST['nama_ibu'], $wa_ortu_clean,
                $file_paths['file_akta'], $file_paths['file_ktp'], $file_paths['file_kk'], $file_paths['file_foto'],
                $user_id
            ]);

            $db->commit();
            
            // Notification for Admin & Sekretaris
            create_notification(null, 'admin', 'Biodata Lengkap', "Santri " . Auth::user()['name'] . " telah melengkapi biodata.", url('admin/santri'));
            create_notification(null, 'sekretaris', 'Biodata Lengkap', "Santri " . Auth::user()['name'] . " telah melengkapi biodata.", url('sekretaris'));

            set_flash_message('success', 'Biodata berhasil dilengkapi! Sekarang Anda dapat mengakses menu ujian.');
        } catch (Exception $e) {
            $db->rollBack();
            set_flash_message('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        redirect('santri');
    }

    public function kirim_video() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf_token($_POST['csrf_token'] ?? '');
            $db = Database::getInstance()->getConnection();
            $user_id = Auth::user()['id'];

            // Initialize ujian_cbt if not exists
            $stmtCheck = $db->prepare("SELECT id FROM ujian_cbt WHERE user_id = ?");
            $stmtCheck->execute([$user_id]);
            if (!$stmtCheck->fetch()) {
                $stmtInsert = $db->prepare("INSERT INTO ujian_cbt (user_id, sudah_kirim_video) VALUES (?, 1)");
                $stmtInsert->execute([$user_id]);
            } else {
                $stmtUpdate = $db->prepare("UPDATE ujian_cbt SET sudah_kirim_video = 1 WHERE user_id = ?");
                $stmtUpdate->execute([$user_id]);
            }

            create_notification(null, 'mufatis', 'Upload Video Hafalan', 'Santri ' . Auth::user()['name'] . ' (' . Auth::user()['username'] . ') telah mengkonfirmasi pengiriman video hafalan.', url('mufatis'));
            create_notification(null, 'admin', 'Upload Video Hafalan', 'Santri ' . Auth::user()['name'] . ' (' . Auth::user()['username'] . ') telah mengkonfirmasi pengiriman video hafalan.', url('admin/cbt'));

            set_flash_message('success', 'Konfirmasi pengiriman video berhasil disimpan. Silakan mulai ujian.');
            redirect('santri');
        }
    }

    public function upload_pembayaran_reg() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf_token($_POST['csrf_token'] ?? '');
            $db = Database::getInstance()->getConnection();
            $user_id = Auth::user()['id'];

            // Upload
            $uploader = new FileUpload('uploads/bukti_bayar');
            $upload_res = $uploader->upload('bukti_transfer');

            if (isset($upload_res['error'])) {
                set_flash_message('error', $upload_res['error']);
                redirect('santri');
            }

            $stmtPengaturan = $db->query("SELECT value FROM pengaturan WHERE `key` = 'biaya_registrasi'");
            $nominal_bayar = $stmtPengaturan->fetchColumn() ?: 200000;

            $stmtInsert = $db->prepare("INSERT INTO pembayaran (user_id, jenis, nominal_bayar, bukti_transfer) VALUES (?, 'registrasi', ?, ?)");
            $stmtInsert->execute([$user_id, $nominal_bayar, $upload_res['filename']]);

            create_notification(null, 'bendahara_reg', 'Pembayaran Registrasi Baru', 'Santri ' . Auth::user()['name'] . ' (' . Auth::user()['username'] . ') mengirim bukti pembayaran registrasi.', url('bendahara-reg'));
            create_notification(null, 'admin', 'Pembayaran Registrasi Baru', 'Santri ' . Auth::user()['name'] . ' (' . Auth::user()['username'] . ') mengirim bukti pembayaran registrasi.', url('admin'));
            create_notification(null, 'sekretaris', 'Pembayaran Registrasi Baru', 'Santri ' . Auth::user()['name'] . ' (' . Auth::user()['username'] . ') mengirim bukti pembayaran registrasi.', url('sekretaris'));

            set_flash_message('success', 'Bukti pembayaran pendaftaran berhasil diunggah. Menunggu verifikasi Panitia (Bendahara).');
            redirect('santri');
        }
    }

    public function upload_pembayaran_du() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf_token($_POST['csrf_token'] ?? '');
            $db = Database::getInstance()->getConnection();
            $user_id = Auth::user()['id'];

            // Clean masking dots from input
            $nominal_bayar = str_replace('.', '', $_POST['nominal_bayar'] ?? '0');
            $nominal_infaq = str_replace('.', '', $_POST['nominal_infaq'] ?? '0');
            
            // Upload
            $uploader = new FileUpload('uploads/bukti_bayar');
            $upload_res = $uploader->upload('bukti_transfer');

            if (isset($upload_res['error'])) {
                set_flash_message('error', $upload_res['error']);
                redirect('santri');
            }

            $stmtInsert = $db->prepare("INSERT INTO pembayaran (user_id, jenis, nominal_bayar, nominal_infaq, bukti_transfer, catatan_santri) VALUES (?, 'daftar_ulang', ?, ?, ?, ?)");
            $stmtInsert->execute([$user_id, $nominal_bayar, $nominal_infaq, $upload_res['filename'], $_POST['catatan_santri'] ?? '']);

            create_notification(null, 'bendahara_du', 'Pembayaran Daftar Ulang Baru', 'Santri ' . Auth::user()['name'] . ' (' . Auth::user()['username'] . ') mengirim bukti Daftar Ulang.', url('bendahara-du/verifikasi'));
            create_notification(null, 'admin', 'Pembayaran Daftar Ulang Baru', 'Santri ' . Auth::user()['name'] . ' (' . Auth::user()['username'] . ') mengirim bukti Daftar Ulang.', url('admin'));

            set_flash_message('success', 'Bukti pembayaran daftar ulang berhasil diunggah. Menunggu verifikasi Bendahara.');
            redirect('santri');
        }
    }

    public function submit_seragam() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf_token($_POST['csrf_token'] ?? '');
            $db = Database::getInstance()->getConnection();
            $user_id = Auth::user()['id'];
            
            $ukuran = $_POST['ukuran'] ?? [];
            $catatan = $_POST['catatan'] ?? '';

            if (empty($ukuran)) {
                set_flash_message('error', 'Ukuran seragam tidak boleh kosong.');
                redirect('santri');
            }

            $ukuran_json = json_encode($ukuran);

            $stmtCheck = $db->prepare("SELECT id FROM seragam WHERE user_id = ?");
            $stmtCheck->execute([$user_id]);

            try {
                $db->beginTransaction();

                if ($stmtCheck->fetch()) {
                    $stmtUpdate = $db->prepare("UPDATE seragam SET detail_ukuran_json = ?, catatan = ? WHERE user_id = ?");
                    $stmtUpdate->execute([$ukuran_json, $catatan, $user_id]);
                } else {
                    $stmtInsert = $db->prepare("INSERT INTO seragam (user_id, detail_ukuran_json, catatan) VALUES (?, ?, ?)");
                    $stmtInsert->execute([$user_id, $ukuran_json, $catatan]);
                }

                // Update status to Selesai
                $stmtStatus = $db->prepare("UPDATE users SET status_psb = 'selesai' WHERE id = ?");
                $stmtStatus->execute([$user_id]);
                
                $_SESSION['status_psb'] = 'selesai';

                $db->commit();
                
                set_flash_message('success', 'Data ukuran seragam berhasil disimpan! Proses pendaftaran Anda dinyatakan telah selesai.');
                redirect('santri');
            } catch (Exception $e) {
                $db->rollBack();
                set_flash_message('error', 'Gagal menyimpan data seragam.');
                redirect('santri');
            }
        }
    }


    public function mulai_cbt() {
        $db = Database::getInstance()->getConnection();
        $user_id = Auth::user()['id'];

        // Load User & Settings
        $stmtUser = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmtUser->execute([$user_id]);
        $user = $stmtUser->fetch();

        $stmtPengaturan = $db->query("SELECT `key`, value FROM pengaturan");
        $settings = [];
        while ($row = $stmtPengaturan->fetch()) { $settings[$row['key']] = $row['value']; }

        // Load Gelombang
        $gelombang = null;
        if ($user['gelombang_id']) {
            $stmtGel = $db->prepare("SELECT * FROM gelombang WHERE id = ?");
            $stmtGel->execute([$user['gelombang_id']]);
            $gelombang = $stmtGel->fetch();
        }

        // Validate Schedule based on Priority:
        // 1. Custom Schedule (User specific)
        // 2. Global Mode (Otomatis / Serempak)
        $now = new DateTime();
        $mode = $settings['mode_ujian'] ?? 'serempak';
        $allowed = false;

        if (!empty($user['jadwal_mulai_kustom']) && !empty($user['jadwal_selesai_kustom'])) {
            // Priority 1: User specific Custom Schedule
            $mulai = new DateTime($user['jadwal_mulai_kustom']);
            $selesai = new DateTime($user['jadwal_selesai_kustom']);
            if ($now >= $mulai && $now <= $selesai) $allowed = true;
        } else {
            // Priority 2: Global Mode
            if ($mode == 'serempak') {
                if ($gelombang && !empty($gelombang['jadwal_ujian_mulai']) && !empty($gelombang['jadwal_ujian_selesai'])) {
                    $mulai = new DateTime($gelombang['jadwal_ujian_mulai']);
                    $selesai = new DateTime($gelombang['jadwal_ujian_selesai']);
                    if ($now >= $mulai && $now <= $selesai) $allowed = true;
                }
            } elseif ($mode == 'otomatis') {
                if (!empty($user['ready_at'])) {
                    $mulai = new DateTime($user['ready_at']);
                    $durasi = $settings['durasi_otomatis_hari'] ?? 3;
                    $selesai = (clone $mulai)->modify("+$durasi days");
                    if ($now >= $mulai && $now <= $selesai) $allowed = true;
                }
            } elseif ($mode == 'custom') {
                // Global mode is custom but this specific user has no custom dates set
                $allowed = false;
            }
        }

        if (!$allowed) {
            set_flash_message('error', 'Waktu ujian Anda tidak valid atau telah berakhir.');
            redirect('santri');
        }

        // Validate user can take exam
        $stmtCheck = $db->prepare("SELECT * FROM ujian_cbt WHERE user_id = ?");
        $stmtCheck->execute([$user_id]);
        $cbt = $stmtCheck->fetch();

        // NEW WORKFLOW VALIDATION
        $stmtBio = $db->prepare("SELECT is_lengkap FROM biodata_santri WHERE user_id = ?");
        $stmtBio->execute([$user_id]);
        $bio = $stmtBio->fetch();

        if (empty($bio) || $bio['is_lengkap'] == 0) {
            set_flash_message('error', 'Anda harus melengkapi Biodata terlebih dahulu.');
            redirect('santri');
        }

        if (!$cbt || !$cbt['sudah_kirim_video']) {
            set_flash_message('error', 'Anda belum mengirimkan video hafalan prasyarat.');
            redirect('santri');
        }

        if ($cbt['waktu_submit']) {
            set_flash_message('warning', 'Anda sudah menyelesaikan ujian ini.');
            redirect('santri');
        }

        // Set start time and duration if not set
        if (!$cbt['waktu_mulai']) {
            $stmtMulai = $db->prepare("UPDATE ujian_cbt SET waktu_mulai = NOW() WHERE user_id = ?");
            $stmtMulai->execute([$user_id]);
            $cbt['waktu_mulai'] = date('Y-m-d H:i:s');
        }

        // Get Soal Bank array
        $gelombang_id = $user['gelombang_id'];
        $stmtSoal = $db->prepare("SELECT id, tipe, pertanyaan, pilihan_json FROM soal_bank WHERE gelombang_id = ? AND is_active = 1 ORDER BY urutan ASC, RAND() LIMIT 10");
        $stmtSoal->execute([$gelombang_id]);
        $soal_list = $stmtSoal->fetchAll();

        // Get pengaturan for duration
        $stmtDur = $db->prepare("SELECT value FROM pengaturan WHERE `key` = 'durasi_ujian_menit'");
        $stmtDur->execute();
        $durasi_menit = $stmtDur->fetchColumn() ?: 45;

        // Calculate end time
        require __DIR__ . '/../views/santri/cbt_exam.php';
    }

    public function submit_cbt() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance()->getConnection();
            $user_id = Auth::user()['id'];

            $jawaban_pg = $_POST['jawaban'] ?? [];
            $skor_pg = 0;

            // Simple Score calculation for PG
            if (!empty($jawaban_pg)) {
                $ids = array_keys($jawaban_pg);
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $stmtCek = $db->prepare("SELECT id, jawaban_benar FROM soal_bank WHERE id IN ($placeholders)");
                $stmtCek->execute($ids);
                while ($row = $stmtCek->fetch()) {
                    if (isset($jawaban_pg[$row['id']]) && $jawaban_pg[$row['id']] === $row['jawaban_benar']) {
                        $skor_pg++;
                    }
                }
                // Converted to score 10-100 (assuming 10 questions = 10 each)
                $skor_pg *= 10;
            }

            // Handle Audio Uploads (if any)
            $rekaman_paths = [];
            if (isset($_FILES['rekaman'])) {
                $uploader = new FileUpload('uploads/rekaman', ['webm', 'ogg', 'mp3', 'wav', 'mp4'], 10485760); // max 10MB
                foreach ($_FILES['rekaman']['name'] as $soal_id => $name) {
                    if ($_FILES['rekaman']['error'][$soal_id] == UPLOAD_ERR_OK) {
                        // Mock single file upload structure to use FileUpload class or handle manually
                        $tmp_name = $_FILES['rekaman']['tmp_name'][$soal_id];
                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                        if (!$ext) $ext = 'webm'; // default webm from MediaRecorder
                        $newFilename = uniqid("rekam_{$user_id}_{$soal_id}_", true) . '.' . $ext;
                        $dest = __DIR__ . '/../uploads/rekaman/' . $newFilename;
                        if (!is_dir(__DIR__ . '/../uploads/rekaman/')) mkdir(__DIR__ . '/../uploads/rekaman/', 0755, true);
                        if (move_uploaded_file($tmp_name, $dest)) {
                            $rekaman_paths[$soal_id] = $newFilename;
                        }
                    }
                }
            }

            $rekaman_json = json_encode($rekaman_paths);
            $jawaban_json = json_encode($jawaban_pg);

            // Update Ujian CBT and User status
            try {
                $db->beginTransaction();

                $stmtUpdate = $db->prepare("UPDATE ujian_cbt SET skor_pg = ?, jawaban_json = ?, rekaman_json = ?, waktu_submit = NOW() WHERE user_id = ?");
                $stmtUpdate->execute([$skor_pg, $jawaban_json, $rekaman_json, $user_id]);

                $stmtStatus = $db->prepare("UPDATE users SET status_psb = 'sudah_ujian' WHERE id = ?");
                $stmtStatus->execute([$user_id]);
                $_SESSION['status_psb'] = 'sudah_ujian';

                $db->commit();
                
                // Notification for Mufatis, Admin & Sekretaris
                create_notification(null, 'mufatis', 'Ujian CBT Selesai', "Santri " . Auth::user()['name'] . " telah menyelesaikan tes tertulis (CBT).", url('mufatis'));
                create_notification(null, 'admin', 'Ujian CBT Selesai', "Santri " . Auth::user()['name'] . " telah menyelesaikan tes tertulis (CBT).", url('admin'));
                create_notification(null, 'sekretaris', 'Ujian CBT Selesai', "Santri " . Auth::user()['name'] . " telah menyelesaikan tes tertulis (CBT).", url('sekretaris'));

                set_flash_message('success', 'Ujian berhasil diselesaikan! Jazakumullah ahsanal jaza. Silakan menunggu hasil pengumuman resmi.');
                redirect('santri');
            } catch (Exception $e) {
                $db->rollBack();
                set_flash_message('error', 'Terjadi kesalahan saat menyimpan ujian.');
                redirect('santri');
            }
        }
    }

    public function cetak_sk() {
        $db = Database::getInstance()->getConnection();
        $user_id = Auth::user()['id'];

        // Ambil data lengkap santri, biodata, dan nama gelombang
        $stmt = $db->prepare("SELECT u.*, b.*, g.nama 
                              FROM users u 
                              LEFT JOIN biodata_santri b ON u.id = b.user_id 
                              LEFT JOIN gelombang g ON u.gelombang_id = g.id 
                              WHERE u.id = ?");
        $stmt->execute([$user_id]);
        $data = $stmt->fetch();

        if (!$data || !in_array($data['status_psb'], ['lulus', 'daftar_ulang', 'selesai'])) {
            die("Maaf, Anda tidak berhak mencetak Surat Keputusan ini.");
        }

        $nama_pesantren = get_pengaturan('nama_pesantren');
        $tahun_ajaran = get_pengaturan('tahun_ajaran');

        require __DIR__ . '/../views/cetak/sk_kelulusan.php';
    }
}
