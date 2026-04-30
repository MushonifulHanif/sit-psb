<?php

class BendaharaRegController {
    
    public function __construct() {
        require_role('bendahara_reg');
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->query("
            SELECT p.*, u.name, u.username, u.status_psb, b.no_wa
            FROM pembayaran p 
            JOIN users u ON p.user_id = u.id 
            LEFT JOIN biodata_santri b ON u.id = b.user_id
            WHERE p.jenis = 'registrasi' 
            ORDER BY p.id DESC
        ");
        $pembayaran = $stmt->fetchAll();

        require __DIR__ . '/../views/bendahara_reg/index.php';
    }

    public function verifikasi($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('bendahara-reg');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $status = $_POST['status'] ?? ''; // diterima atau ditolak
        $catatan = $_POST['catatan'] ?? '';

        if (!in_array($status, ['diterima', 'ditolak'])) {
            set_flash_message('error', 'Status tidak valid.');
            redirect('bendahara-reg');
        }

        $db = Database::getInstance()->getConnection();
        
        // Cek Pembayaran
        $stmt = $db->prepare("
            SELECT p.*, u.name 
            FROM pembayaran p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = ? AND p.jenis = 'registrasi'
        ");
        $stmt->execute([$id]);
        $p = $stmt->fetch();

        if (!$p) {
            set_flash_message('error', 'Data pembayaran tidak ditemukan.');
            redirect('bendahara-reg');
        }

        try {
            $db->beginTransaction();

            // Update pembayaran status
            $stmtUpdate = $db->prepare("UPDATE pembayaran SET status = ?, catatan_verifikasi = ?, verified_by = ?, verified_at = NOW() WHERE id = ?");
            $stmtUpdate->execute([$status, $catatan, Auth::user()['id'], $id]);

            // Jika diterima, update status santri ke 'siap_ujian' JIKA masih 'pendaftar'
            if ($status == 'diterima') {
                $stmtCek = $db->prepare("SELECT status_psb FROM users WHERE id = ?");
                $stmtCek->execute([$p['user_id']]);
                if ($stmtCek->fetchColumn() == 'pendaftar') {
                    $stmtUser = $db->prepare("UPDATE users SET status_psb = 'siap_ujian', ready_at = NOW() WHERE id = ?");
                    $stmtUser->execute([$p['user_id']]);
                }
            } else {
                 // jika ditolak dan status masih pendaftar, biarkan tetap pendaftar
                $stmtCek = $db->prepare("SELECT status_psb FROM users WHERE id = ?");
                $stmtCek->execute([$p['user_id']]);
                // Bisa tambahkan notif atau tindakan lain
            }

            // Notification
            if($status == 'diterima') {
                create_notification(
                    $p['user_id'], 
                    'santri', 
                    'Pembayaran Validasi Berhasil', 
                    'Pembayaran Pendaftaran Anda telah kami terima. Anda sekarang bisa mengakses Ujian Seleksi.', 
                    url('santri')
                );
                // Also notify Admin & Sekretaris
                create_notification(null, 'admin', 'Pembayaran Terverifikasi', "Pembayaran registrasi santri " . $p['name'] . " telah diverifikasi.", url('admin'));
                create_notification(null, 'sekretaris', 'Pembayaran Terverifikasi', "Pembayaran registrasi santri " . $p['name'] . " telah diverifikasi. Siap untuk penjadwalan.", url('sekretaris'));
            } else {
                create_notification(
                    $p['user_id'], 
                    'santri', 
                    'Pembayaran Ditolak', 
                    "Verifikasi pembayaran gagal. Alasan: $catatan", 
                    url('santri')
                );
                // Also notify Admin
                create_notification(null, 'admin', 'Pembayaran Ditolak', "Pembayaran registrasi santri " . $p['name'] . " ditolak.", url('admin'));
            }

            $db->commit();
            set_flash_message('success', 'Pembayaran berhasil diverifikasi.');

        } catch (Exception $e) {
            $db->rollBack();
            set_flash_message('error', 'Gagal memverifikasi pembayaran.');
        }

        redirect('bendahara-reg');
    }
}
