<?php

class AuthController {
    public function index() {
        $this->login();
    }

    public function login() {
        require_guest();
        require __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        require_guest();
        require __DIR__ . '/../views/auth/register.php';
    }

    public function do_login() {
        require_guest();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('auth/login');

        // Brute-force protection
        $ip = md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $failKey = 'login_fail_' . $ip;
        $lockKey = 'login_lock_' . $ip;
        
        if (!empty($_SESSION[$lockKey]) && $_SESSION[$lockKey] > time()) {
            $sisa = ceil(($_SESSION[$lockKey] - time()) / 60);
            set_flash_message('error', "Terlalu banyak percobaan gagal. Silakan tunggu {$sisa} menit lagi.");
            redirect('auth/login');
            return;
        }
        
        verify_csrf_token($_POST['csrf_token'] ?? '');

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            set_flash_message('error', 'Username dan password harus diisi.');
            redirect('auth/login');
            return;
        }

        if (Auth::login($username, $password)) {
            // Reset counters on success
            unset($_SESSION[$failKey], $_SESSION[$lockKey]);
            
            set_flash_message('success', 'Login berhasil. Selamat datang!');
            // Redirect based on role
            if (Auth::isSantri()) {
                redirect('santri');
            } else {
                $role = Auth::user()['role'];
                redirect(str_replace('_', '-', $role));
            }
        } else {
            // Increment fail counter
            $_SESSION[$failKey] = ($_SESSION[$failKey] ?? 0) + 1;
            if ($_SESSION[$failKey] >= 5) {
                $_SESSION[$lockKey] = time() + (15 * 60); // Lock 15 menit
                $_SESSION[$failKey] = 0;
                set_flash_message('error', 'Terlalu banyak percobaan gagal. Akun dikunci sementara.');
            } else {
                $sisaCoba = 5 - $_SESSION[$failKey];
                set_flash_message('error', "Username atau password salah. Sisa percobaan: {$sisaCoba}");
            }
            redirect('auth/login');
        }
    }

    public function do_register() {
        require_guest();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('auth/register');
        
        verify_csrf_token($_POST['csrf_token'] ?? '');

        $val = new Validator();
        $val->required($_POST, ['name', 'jk', 'no_wa', 'asal_sekolah', 'password', 'password_confirm']);
        $val->minLength($_POST, 'password', 6);

        if ($_POST['password'] !== $_POST['password_confirm']) {
            $val->setError('password', 'Konfirmasi password tidak cocok.');
        }

        if ($val->hasErrors()) {
            $_SESSION['old'] = $_POST;
            set_flash_message('error', implode('<br>', $val->getErrors()));
            redirect('auth/register');
        }

        $db = Database::getInstance()->getConnection();
        
        // Generate Username (Nomor Tes) e.g., PSB-001
        $stmt = $db->query("SELECT id FROM users WHERE role = 'santri' ORDER BY id DESC LIMIT 1");
        $lastSantri = $stmt->fetch();
        $nextId = $lastSantri ? $lastSantri['id'] + 1 : 1;
        $username = 'PSB-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Cari gelombang aktif
        $stmtGel = $db->query("SELECT id FROM gelombang WHERE is_active = 1 LIMIT 1");
        $gelombang = $stmtGel->fetch();
        $gelombang_id = $gelombang ? $gelombang['id'] : null;

        try {
            $db->beginTransaction();

            $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
            
            $stmtUser = $db->prepare("INSERT INTO users (name, username, password, password_plain, role, status_psb, gelombang_id) VALUES (?, ?, ?, ?, 'santri', 'pendaftar', ?)");
            $stmtUser->execute([$_POST['name'], $username, $passwordHash, $_POST['password'], $gelombang_id]);
            
            $userId = $db->lastInsertId();

            $no_wa_clean = preg_replace('/[^0-9]/', '', $_POST['no_wa'] ?? '');
            $stmtBio = $db->prepare("INSERT INTO biodata_santri (user_id, jk, no_wa, asal_sekolah) VALUES (?, ?, ?, ?)");
            $stmtBio->execute([$userId, $_POST['jk'], $no_wa_clean, $_POST['asal_sekolah']]);

            $db->commit();

            // Notification for Admin & Sekretaris
            create_notification(null, 'admin', 'Pendaftaran Baru', "Santri baru: {$_POST['name']} ({$username}) telah mendaftar.", url('admin/santri'));
            create_notification(null, 'sekretaris', 'Pendaftaran Baru', "Santri baru: {$_POST['name']} ({$username}) telah mendaftar.", url('sekretaris'));

            // Set session for generated account details temporary
            $_SESSION['new_account'] = [
                'name' => $_POST['name'],
                'username' => $username,
                'password' => $_POST['password'], // plain text only for printing
                'gelombang' => $gelombang_id
            ];

            redirect('auth/success');

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['old'] = $_POST;
            set_flash_message('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            redirect('auth/register');
        }
    }

    public function logout() {
        Auth::logout();
        set_flash_message('success', 'Anda berhasil logout.');
        redirect('auth/login');
    }

    public function success() {
        if (!isset($_SESSION['new_account'])) {
            redirect('auth/login');
        }
        $account = $_SESSION['new_account'];
        require __DIR__ . '/../views/auth/success.php';
    }

    public function cetak_kartu() {
        if (!isset($_SESSION['new_account'])) {
            redirect('auth/login');
        }
        $account = $_SESSION['new_account'];
        require __DIR__ . '/../views/auth/cetak_kartu.php';
    }
}
