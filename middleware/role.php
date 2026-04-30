<?php

require_once __DIR__ . '/auth.php';

function require_role($roles) {
    require_auth();
    if (!Auth::hasRole($roles)) {
        http_response_code(403);
        die("<h1>403 Forbidden</h1><p>Anda tidak memiliki hak akses ke halaman ini.</p><a href='".url('')."'>Kembali ke beranda</a>");
    }
}
