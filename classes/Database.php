<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Attempt to create database if it doesn't exist
            if ($e->getCode() == 1049) { // Unknown database error code
                try {
                    $dsn_no_db = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
                    $pdo_no_db = new PDO($dsn_no_db, DB_USER, DB_PASS);
                    $pdo_no_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo_no_db->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
                    
                    // Reconnect
                    $options = [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ];
                    $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                } catch (PDOException $e2) {
                     die("Connection failed (and could not create DB): " . $e2->getMessage());
                }
            } else {
                die("Connection failed: " . $e->getMessage());
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
