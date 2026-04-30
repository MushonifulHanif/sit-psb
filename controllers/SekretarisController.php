<?php

class SekretarisController {

    public function __construct() {
        require_role('sekretaris');
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        
        $total_pendaftar = $db->query("SELECT COUNT(*) FROM users WHERE role='santri'")->fetchColumn();
        $total_l = $db->query("SELECT COUNT(*) FROM users u JOIN biodata_santri b ON u.id = b.user_id WHERE u.role='santri' AND b.jk='L'")->fetchColumn();
        $total_p = $db->query("SELECT COUNT(*) FROM users u JOIN biodata_santri b ON u.id = b.user_id WHERE u.role='santri' AND b.jk='P'")->fetchColumn();
        
        $total_lulus = $db->query("SELECT COUNT(*) FROM users WHERE role='santri' AND status_psb IN ('lulus','daftar_ulang','selesai')")->fetchColumn();
        $total_gagal = $db->query("SELECT COUNT(*) FROM users WHERE role='santri' AND status_psb = 'gagal'")->fetchColumn();
        
        require __DIR__ . '/../views/sekretaris/dashboard.php';
    }

    public function pendaftar() {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->query("
            SELECT b.*, 
                   u.id, u.name, u.username, u.password_plain, u.status_psb, 
                   u.jadwal_mulai_kustom, u.jadwal_selesai_kustom, u.mufatis_id
            FROM users u
            LEFT JOIN biodata_santri b ON u.id = b.user_id
            WHERE u.role = 'santri'
            ORDER BY u.id DESC
        ");
        $santri = $stmt->fetchAll();

        // Get mode_ujian to show relevant info in view
        $stmtMode = $db->query("SELECT value FROM pengaturan WHERE `key` = 'mode_ujian'");
        $mode_ujian = $stmtMode->fetchColumn() ?: 'serempak';
        
        // Get list of Mufatis for assignment
        $stmtMufatis = $db->query("SELECT id, name FROM users WHERE role = 'mufatis' ORDER BY name ASC");
        $mufatis_list = $stmtMufatis->fetchAll();
 
        require __DIR__ . '/../views/sekretaris/index.php';
    }

    public function update_jadwal_kustom() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('sekretaris/pendaftar');
        verify_csrf_token($_POST['csrf_token'] ?? '');

        $db = Database::getInstance()->getConnection();
        $user_id = $_POST['user_id'];
        $mulai = $_POST['jadwal_mulai'] ?? null;
        $selesai = $_POST['jadwal_selesai'] ?? null;
        $mufatis_id = $_POST['mufatis_id'] ?? null;
 
        $stmt = $db->prepare("UPDATE users SET jadwal_mulai_kustom = ?, jadwal_selesai_kustom = ?, mufatis_id = ? WHERE id = ?");
        $stmt->execute([
            !empty($mulai) ? str_replace('T', ' ', $mulai) : null,
            !empty($selesai) ? str_replace('T', ' ', $selesai) : null,
            !empty($mufatis_id) ? $mufatis_id : null,
            $user_id
        ]);

        set_flash_message('success', 'Jadwal ujian kustom berhasil diperbarui.');
        redirect('sekretaris/pendaftar');
    }

    public function reset_ujian() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('sekretaris/pendaftar');
        verify_csrf_token($_POST['csrf_token'] ?? '');

        $db = Database::getInstance()->getConnection();
        $user_id = $_POST['user_id'];

        try {
            $db->beginTransaction();

            // Reset Status PSB
            $stmtStatus = $db->prepare("UPDATE users SET status_psb = 'siap_ujian' WHERE id = ?");
            $stmtStatus->execute([$user_id]);

            // Clear CBT Progress
            $stmtCBT = $db->prepare("UPDATE ujian_cbt SET waktu_mulai = NULL, waktu_submit = NULL, skor_pg = 0, jawaban_json = NULL, rekaman_json = NULL WHERE user_id = ?");
            $stmtCBT->execute([$user_id]);

            $db->commit();
            set_flash_message('success', 'Ujian santri berhasil di-reset. Santri dapat menempuh ujian kembali.');
        } catch (Exception $e) {
            $db->rollBack();
            set_flash_message('error', 'Gagal me-reset ujian.');
        }

        redirect('sekretaris/pendaftar');
    }
    
    public function update_password() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('sekretaris/pendaftar');
        verify_csrf_token($_POST['csrf_token'] ?? '');

        $db = Database::getInstance()->getConnection();
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];

        if (strlen($password) < 6) {
            set_flash_message('error', 'Password minimal 6 karakter.');
            redirect('sekretaris/pendaftar');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password = ?, password_plain = ? WHERE id = ?");
        $stmt->execute([$passwordHash, $password, $user_id]);

        set_flash_message('success', 'Password santri berhasil diperbarui.');
        redirect('sekretaris/pendaftar');
    }

    public function export() {
        require __DIR__ . '/../views/sekretaris/export.php';
    }

    public function do_export($type) {
        $db = Database::getInstance()->getConnection();
        
        if ($type == 'semua_pendaftar') {
            $stmt = $db->query("
                SELECT u.username as nomor_tes, u.name as nama, b.jk, b.tempat_lahir, b.tgl_lahir as tanggal_lahir, 
                       b.jenjang, b.nama_panggilan, b.nisn, b.kewarganegaraan, b.jumlah_saudara,
                       b.alamat_lengkap, b.asal_sekolah, b.nama_ayah, b.nama_ibu, b.wa_ortu, b.no_wa,
                       u.status_psb 
                FROM users u 
                LEFT JOIN biodata_santri b ON u.id = b.user_id 
                WHERE u.role = 'santri' ORDER BY u.id ASC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "Export_Semua_Pendaftar_" . date('Ymd_His') . ".xls";
        } elseif ($type == 'nilai_cbt') {
            $stmt = $db->query("
                SELECT u.username as nomor_tes, u.name as nama, c.skor_pg, l.nilai as nilai_lisan, u.status_psb 
                FROM users u 
                JOIN ujian_cbt c ON u.id = c.user_id 
                LEFT JOIN ujian_lisan l ON u.id = l.user_id
                WHERE u.status_psb IN ('sudah_ujian', 'lulus', 'gagal', 'daftar_ulang', 'selesai')
            ");
            $data = $stmt->fetchAll();
            $filename = "Export_Nilai_CBT_" . date('Ymd_His') . ".xls";
        } elseif ($type == 'seragam') {
             // Dynamic query for seragam depending on items is harder in plain SQL, we'll process in PHP
             // First get item headers
             $stmtItems = $db->query("SELECT id, nama_item FROM item_seragam ORDER BY urutan ASC");
             $items = $stmtItems->fetchAll();
             
             $stmt = $db->query("
                SELECT u.username as nomor_tes, u.name as nama, b.jk, s.detail_ukuran_json, s.catatan
                FROM users u 
                JOIN seragam s ON u.id = s.user_id 
                JOIN biodata_santri b ON u.id = b.user_id
             ");
             $raw_data = $stmt->fetchAll();
             
             $data = [];
             foreach ($raw_data as $row) {
                 $ukuran = json_decode($row['detail_ukuran_json'], true);
                 $flat_row = [
                     'nomor_tes' => $row['nomor_tes'],
                     'nama' => $row['nama'],
                     'jk' => $row['jk']
                 ];
                 foreach ($items as $item) {
                     $flat_row["ukuran_" . str_replace(' ', '_', $item['nama_item'])] = $ukuran[$item['id']] ?? '-';
                 }
                 $flat_row['catatan'] = $row['catatan'];
                 $data[] = $flat_row;
             }
             $filename = "Export_Data_Seragam_" . date('Ymd_His') . ".xls";
        } else {
             die("Tipe export tidak valid.");
        }

        // Output as HTML disguised as XLS formatting
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache"); 
        header("Expires: 0");

        if (count($data) > 0) {
            echo '<table border="1">';
            echo '<tr>';
            foreach (array_keys($data[0]) as $k) {
                echo '<th style="background-color: #d1fae5; font-weight: bold;">' . htmlspecialchars($k) . '</th>';
            }
            echo '</tr>';
            foreach ($data as $row) {
                echo '<tr>';
                foreach ($row as $v) {
                    echo '<td>' . htmlspecialchars($v ?? '') . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<table border="1"><tr><td>Data Kosong</td></tr></table>';
        }
        
        exit;
    }
}
