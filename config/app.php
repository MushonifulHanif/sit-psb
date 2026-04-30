<?php

// Konfigurasi Aplikasi (Otomatis mendeteksi Host/IP)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('APP_URL', $protocol . $host . $baseDir);

// Helper functions
function url($path = '') {
    return APP_URL . ($path ? '/' . ltrim($path, '/') : '');
}

function asset($path) {
    if (strpos($path, 'uploads/') === 0) {
        return url($path);
    }
    return url('assets/' . ltrim($path, '/'));
}

function redirect($path) {
    header("Location: " . url($path));
    exit;
}

// Security: CSRF Token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die("Invalid CSRF token.");
    }
}

// Flash Messages
function set_flash_message($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function display_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        
        $color = 'blue';
        if ($flash['type'] == 'success') $color = 'green';
        if ($flash['type'] == 'error') $color = 'red';
        if ($flash['type'] == 'warning') $color = 'yellow';

        return "<div class='bg-{$color}-100 border border-{$color}-400 text-{$color}-700 px-4 py-3 rounded relative mb-4' role='alert'>
                    <span class='block sm:inline'>{$flash['message']}</span>
                </div>";
    }
    return '';
}

// Sanitasi output HTML (untuk WYSIWYG)
function safe_html($input) {
    if (empty($input)) return '';
    // Izinkan tag HTML dasar untuk formatting, blokir script, iframe, js event, dll.
    $allowed = '<p><br><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><span><div><b><i><u><s><blockquote>';
    return strip_tags($input, $allowed);
}

// Get Pengaturan from Database
function get_pengaturan($key) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT value FROM pengaturan WHERE `key` = ?");
    $stmt->execute([$key]);
    if ($row = $stmt->fetch()) {
        return $row['value'];
    }
    return null;
}

function create_notification($target_user_id, $target_role, $judul, $pesan, $link = '#') {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO notifikasi (target_user_id, target_role, judul, pesan, link, read_by_json) VALUES (?, ?, ?, ?, ?, '[]')");
    $stmt->execute([$target_user_id, $target_role, $judul, $pesan, $link]);

    // TRIGGER WEB PUSH (Kirim Notifikasi ke HP)
    send_web_push($target_user_id, $target_role, $judul, $pesan, $link);
}

function send_web_push($target_user_id, $target_role, $judul, $pesan, $link) {
    $db = Database::getInstance()->getConnection();
    
    // Cari semua token HP (subscriptions) yang relevan
    $sql = "SELECT * FROM push_subscriptions WHERE 1=1";
    $params = [];
    
    if ($target_user_id) {
        $sql .= " AND user_id = ?";
        $params[] = $target_user_id;
    } elseif ($target_role && $target_role !== 'semua') {
        $sql .= " AND user_id IN (SELECT id FROM users WHERE role = ?)";
        $params[] = $target_role;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $subscriptions = $stmt->fetchAll();

    if (empty($subscriptions)) return;

    // Persiapkan Library WebPush
    require_once __DIR__ . '/../vendor/autoload.php';
    $auth = [
        'VAPID' => [
            'subject' => VAPID_SUBJECT,
            'publicKey' => VAPID_PUBLIC_KEY,
            'privateKey' => VAPID_PRIVATE_KEY,
        ],
    ];

    $webPush = new \Minishlink\WebPush\WebPush($auth);
    $payload = json_encode([
        'title' => $judul,
        'body' => $pesan,
        'icon' => asset('img/logo.png'), // Pastikan file logo ada
        'data' => ['url' => $link]
    ]);

    foreach ($subscriptions as $sub) {
        $subscription = \Minishlink\WebPush\Subscription::create([
            'endpoint' => $sub['endpoint'],
            'publicKey' => $sub['p256dh_key'],
            'authToken' => $sub['auth_key'],
        ]);

        $webPush->queueNotification($subscription, $payload);
    }

    // Eksekusi pengiriman massal
    foreach ($webPush->flush() as $report) {
        if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
            // Hapus token yang sudah kadaluarsa dari database agar tidak berat
            $db->prepare("DELETE FROM push_subscriptions WHERE endpoint = ?")->execute([$report->getEndpoint()]);
        }
    }
}

// Regional Formatting Helpers
function format_indo_date($date, $format = 'd F Y') {
    if (!$date) return '-';
    
    $timestamp = strtotime($date);
    $formatted = date($format, $timestamp);
    
    // Replace English months with Indonesian
    $months = [
        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
        'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
        'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
    ];
    
    return strtr($formatted, $months);
}

function format_indo_datetime($datetime) {
    if (!$datetime) return '-';
    return date('d/m/Y H:i', strtotime($datetime));
}

function format_rupiah($amount) {
    if ($amount === null || $amount === '') return 'Rp 0';
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}

function get_santri_step_results($user_id) {
    $db = Database::getInstance()->getConnection();
    
    // Get User status
    $stmt = $db->prepare("SELECT status_psb FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $status = $user['status_psb'] ?? 'pendaftar';

    // Get Biodata
    $stmt = $db->prepare("SELECT is_lengkap FROM biodata_santri WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $biodata = $stmt->fetch();

    // Get CBT
    $stmt = $db->prepare("SELECT waktu_submit FROM ujian_cbt WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cbt = $stmt->fetch();

    // Get Payment DU
    $stmt = $db->prepare("SELECT SUM(nominal_bayar) as total FROM pembayaran WHERE user_id = ? AND jenis = 'daftar_ulang' AND status = 'diterima'");
    $stmt->execute([$user_id]);
    $pay = $stmt->fetch();
    $total_bayar_du = floatval($pay['total'] ?? 0);

    // Get DU Cost from Pengaturan
    $stmt = $db->prepare("SELECT value FROM pengaturan WHERE `key` = 'biaya_daftar_ulang'");
    $stmt->execute();
    $row = $stmt->fetch();
    $biaya_du = floatval($row['value'] ?? 0);

    // Get Seragam
    $stmt = $db->prepare("SELECT id FROM seragam WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $seragam = $stmt->fetch();

    return [
        1 => ['completed' => ($biodata['is_lengkap'] ?? 0) == 1],
        2 => ['completed' => !empty($cbt['waktu_submit'])],
        3 => ['completed' => in_array($status, ['lulus', 'daftar_ulang', 'selesai'])],
        4 => ['completed' => ($biaya_du > 0 && $total_bayar_du >= $biaya_du)],
        5 => ['completed' => !empty($seragam)],
    ];
}

function calculate_current_step($step_results) {
    $current_step = 1;
    if (!$step_results[1]['completed']) {
        $current_step = 1;
    } elseif (!$step_results[2]['completed']) {
        $current_step = 2;
    } elseif (!$step_results[3]['completed']) {
        $current_step = 3;
    } elseif (!$step_results[4]['completed'] || !$step_results[5]['completed']) {
        if (!$step_results[4]['completed']) {
            $current_step = 4;
        } else {
            $current_step = 5;
        }
    } else {
        $current_step = 5;
    }
    return $current_step;
}
