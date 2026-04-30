<?php

class BendaharaDUController {
    
    public function __construct() {
        require_role('bendahara_du');
    }

    public function index() {
        // Tracking Piutang
        $db = Database::getInstance()->getConnection();
        
        $biaya_du = floatval(get_pengaturan('biaya_daftar_ulang') ?: 0);
        
        // Find users with status lulus, daftar_ulang, or selesai
        $stmt = $db->query("
            SELECT u.id, u.name, u.username, u.status_psb,
                   COALESCE((SELECT SUM(nominal_bayar) FROM pembayaran WHERE user_id = u.id AND jenis = 'daftar_ulang' AND status = 'diterima'), 0) as total_dibayar,
                   COALESCE((SELECT SUM(nominal_infaq) FROM pembayaran WHERE user_id = u.id AND jenis = 'daftar_ulang' AND status = 'diterima'), 0) as total_infaq
            FROM users u
            WHERE u.status_psb IN ('lulus', 'daftar_ulang', 'selesai')
            ORDER BY u.name ASC
        ");
        $santri = $stmt->fetchAll();

        require __DIR__ . '/../views/bendahara_du/index.php';
    }

    public function verifikasi() {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->query("
            SELECT p.*, u.name, u.username 
            FROM pembayaran p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.jenis = 'daftar_ulang' 
            ORDER BY p.status = 'pending' DESC, p.id DESC
        ");
        $pembayaran = $stmt->fetchAll();

        require __DIR__ . '/../views/bendahara_du/verifikasi.php';
    }

    public function do_verifikasi($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('bendahara-du/verifikasi');
        verify_csrf_token($_POST['csrf_token'] ?? '');
        
        $status = $_POST['status'] ?? ''; // diterima atau ditolak
        $catatan = $_POST['catatan'] ?? '';

        if (!in_array($status, ['diterima', 'ditolak'])) {
            set_flash_message('error', 'Status tidak valid.');
            redirect('bendahara-du/verifikasi');
        }

        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT p.*, u.name 
            FROM pembayaran p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = ? AND p.jenis = 'daftar_ulang'
        ");
        $stmt->execute([$id]);
        $p = $stmt->fetch();

        if (!$p) {
            set_flash_message('error', 'Data pembayaran tidak ditemukan.');
            redirect('bendahara-du/verifikasi');
        }

        try {
            $db->beginTransaction();

            $stmtUpdate = $db->prepare("UPDATE pembayaran SET status = ?, catatan_verifikasi = ?, verified_by = ?, verified_at = NOW() WHERE id = ?");
            $stmtUpdate->execute([$status, $catatan, Auth::user()['id'], $id]);

            if ($status == 'diterima') {
                $stmtUser = $db->prepare("SELECT status_psb FROM users WHERE id = ?");
                $stmtUser->execute([$p['user_id']]);
                if ($stmtUser->fetchColumn() == 'lulus') {
                    $stmtUserUp = $db->prepare("UPDATE users SET status_psb = 'daftar_ulang' WHERE id = ?");
                    $stmtUserUp->execute([$p['user_id']]);
                }
            }

            // Notification
            if($status == 'diterima') {
                create_notification(
                    $p['user_id'], 
                    'santri', 
                    'Verifikasi Daftar Ulang & Infaq Diterima', 
                    'Terima kasih! Bukti transfer Anda telah diverifikasi. Anda sekarang bisa mengakses fitur E-Seragam.', 
                    url('santri')
                );
                // Also notify Admin
                create_notification(null, 'admin', 'Verifikasi DU Diterima', "Daftar ulang santri " . $p['name'] . " telah diverifikasi Bendahara DU.", url('admin'));
            } else {
                create_notification(
                    $p['user_id'], 
                    'santri', 
                    'Verifikasi Pembayaran Ditolak', 
                    "Transaksi Anda ditolak dengan catatan: $catatan. Mohon periksa kembali bukti yang diunggah.", 
                    url('santri')
                );
                // Also notify Admin
                create_notification(null, 'admin', 'Verifikasi DU Ditolak', "Daftar ulang santri " . $p['name'] . " ditolak oleh Bendahara DU.", url('admin/pembayaran'));
            }

            $db->commit();
            set_flash_message('success', 'Verifikasi berhasil.');

        } catch (Exception $e) {
            $db->rollBack();
            set_flash_message('error', 'Gagal memverifikasi pembayaran.');
        }

        redirect('bendahara-du/verifikasi');
    }
}
