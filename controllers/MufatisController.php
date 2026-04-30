<?php

class MufatisController {

    public function __construct() {
        require_role('mufatis');
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        
        // Daftar santri yang sudah ujian tertulis (dan memiliki rekaman audio) atau siap lisan
        $stmt = $db->query("
            SELECT u.id, u.name, u.username, u.status_psb, 
                   c.skor_pg, c.rekaman_json,
                   l.nilai, l.catatan as lisan_catatan
            FROM users u
            JOIN ujian_cbt c ON u.id = c.user_id
            LEFT JOIN ujian_lisan l ON u.id = l.user_id
            WHERE u.status_psb IN ('sudah_ujian', 'lulus', 'gagal')
            ORDER BY c.waktu_submit DESC
        ");
        $santri = $stmt->fetchAll();

        require __DIR__ . '/../views/mufatis/index.php';
    }

    public function simpan_nilai($user_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('mufatis');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $nilai = floatval($_POST['nilai'] ?? 0);
        $catatan = $_POST['catatan'] ?? '';

        $db = Database::getInstance()->getConnection();
        $mufatis_id = Auth::user()['id'];

        $stmtCek = $db->prepare("SELECT id FROM ujian_lisan WHERE user_id = ?");
        $stmtCek->execute([$user_id]);

        if ($stmtCek->fetch()) {
            $stmtUpdate = $db->prepare("UPDATE ujian_lisan SET mufatis_id = ?, nilai = ?, catatan = ? WHERE user_id = ?");
            $stmtUpdate->execute([$mufatis_id, $nilai, $catatan, $user_id]);
        } else {
            $stmtInsert = $db->prepare("INSERT INTO ujian_lisan (user_id, mufatis_id, nilai, catatan) VALUES (?, ?, ?, ?)");
            $stmtInsert->execute([$user_id, $mufatis_id, $nilai, $catatan]);
        }

        set_flash_message('success', 'Nilai lisan / hafalan berhasil disimpan.');
        redirect('mufatis');
    }

    public function kelulusan() {
        $db = Database::getInstance()->getConnection();
        
        // Tampilkan santri yang sudah punya nilai lisan dan belum diputus kelulusannya
        // Atau semua yang sudah siap diverifikasi kelulusan
        $stmt = $db->query("
            SELECT u.id, u.name, u.username, u.status_psb, 
                   c.skor_pg, l.nilai as nilai_lisan,
                   l.status_kelulusan
            FROM users u
            JOIN ujian_cbt c ON u.id = c.user_id
            JOIN ujian_lisan l ON u.id = l.user_id
            WHERE u.status_psb IN ('sudah_ujian', 'lulus', 'gagal')
            ORDER BY u.id DESC
        ");
        $santri = $stmt->fetchAll();

        require __DIR__ . '/../views/mufatis/kelulusan.php';
    }

    public function proses_kelulusan() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('mufatis/kelulusan');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $user_id = $_POST['user_id'] ?? 0;
        $status = $_POST['status'] ?? ''; // lulus atau gagal

        if (!in_array($status, ['lulus', 'gagal'])) {
            set_flash_message('error', 'Status kelulusan tidak valid.');
            redirect('mufatis/kelulusan');
        }

        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            $stmtStatus = $db->prepare("UPDATE users SET status_psb = ? WHERE id = ?");
            $stmtStatus->execute([$status, $user_id]);

            $stmtLisan = $db->prepare("UPDATE ujian_lisan SET status_kelulusan = ? WHERE user_id = ?");
            $stmtLisan->execute([$status, $user_id]);

            // Generic Notification (so user has to check dashboard to see pass/fail)
            create_notification(
                $user_id, 
                'santri', 
                'Hasil Pengumuman Seleksi PSB', 
                'Alhamdulillah, seluruh tahapan ujian seleksi Anda telah selesai dinilai. Silakan masuk ke dashboard untuk melihat hasil pengumuman resmi.', 
                url('santri')
            );

            // Also notify Admin
            create_notification(null, 'admin', 'Status Kelulusan Ditetapkan', "Status kelulusan untuk suser ID #$user_id telah ditetapkan sebagai: " . strtoupper($status), url('admin/santri'));

            $db->commit();
            set_flash_message('success', "Status santri berhasil diubah menjadi: " . strtoupper($status));

        } catch (Exception $e) {
            $db->rollBack();
            set_flash_message('error', 'Gagal menyimpan status kelulusan.');
        }

        redirect('mufatis/kelulusan');
    }
}
