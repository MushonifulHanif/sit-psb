<?php

// Security headers for sessions
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 2592000); // 30 hari dalam detik
ini_set('session.gc_maxlifetime', 2592000);  // 30 hari dalam detik

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

ini_set('session.cookie_samesite', 'Lax');

session_start();

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/vapid.php';
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/role.php';

// Autoload classes (basic implementation)
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Set default timezone
date_default_timezone_set('Asia/Jakarta');
