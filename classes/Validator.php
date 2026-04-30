<?php

class Validator {
    private $errors = [];

    // Validasi required
    public function required($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' harus diisi.';
            }
        }
    }

    // Validasi email
    public function email($data, $field) {
        if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Format email tidak valid.';
        }
    }

    // Validasi panjang minimum
    public function minLength($data, $field, $min) {
        if (!empty($data[$field]) && strlen($data[$field]) < $min) {
            $this->errors[$field] = ucfirst($field) . " minimal $min karakter.";
        }
    }
    
    // Validasi nilai angka
    public function numeric($data, $field) {
        if (!empty($data[$field]) && !is_numeric($data[$field])) {
             $this->errors[$field] = ucfirst($field) . " harus berupa angka.";
        }
    }

    // Periksa apakah data unik di tabel
    public function unique($table, $column, $value, $except_id = null) {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT id FROM $table WHERE $column = ?";
        $params = [$value];

        if ($except_id) {
            $sql .= " AND id != ?";
            $params[] = $except_id;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        if ($stmt->fetch()) {
             $this->errors[$column] = ucfirst($column) . ' sudah terdaftar.';
        }
    }

    // Cek ada error
    public function hasErrors() {
        return count($this->errors) > 0;
    }

    // Ambil error
    public function getErrors() {
        return $this->errors;
    }
    
    // Set custom error
    public function setError($field, $message) {
        $this->errors[$field] = $message;
    }
}
