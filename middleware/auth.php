<?php

function require_auth() {
    if (!Auth::check()) {
        set_flash_message('warning', 'Silakan login terlebih dahulu.');
        redirect('auth/login');
    }
}

function require_guest() {
    if (Auth::check()) {
        if (Auth::isSantri()) {
            redirect('santri');
        } else {
            $user = Auth::user();
            redirect(str_replace('_', '-', $user['role'])); // admin, sekretaris, bendahara-reg, dll
        }
    }
}
