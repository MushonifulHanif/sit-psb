<?php

class Auth {

    // Login function
    public static function login($username, $password) {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Setup Session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['status_psb'] = $user['status_psb'];
            
            return true;
        }

        return false;
    }

    // Check if logged in
    public static function check() {
        return isset($_SESSION['user_id']);
    }

    // Get current user details
    public static function user() {
        if (!self::check()) return null;
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }

    // Check specific role
    public static function hasRole($role) {
        if (!self::check()) return false;
        
        if (is_array($role)) {
            return in_array($_SESSION['role'], $role);
        }
        return $_SESSION['role'] === $role;
    }

    // Check if user is 'admin' (can access some special stuff)
    public static function isAdmin() {
        return self::hasRole('admin');
    }

    // Check if user is 'santri'
    public static function isSantri() {
         return self::hasRole('santri');
    }

    // Logout
    public static function logout() {
        session_unset();
        session_destroy();
    }
}
