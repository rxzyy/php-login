<?php
require_once 'config.php';

// Cek apakah fungsi sudah ada sebelum dideklarasikan
if (!function_exists('generateSalt')) {
    function generateSalt($length = 16) {
        return bin2hex(random_bytes($length));
    }
}

if (!function_exists('hashPassword')) {
    function hashPassword($password, $salt) {
        return hash('sha256', $password . $salt);
    }
}

if (!function_exists('validatePassword')) {
    function validatePassword($password) {
        // Minimum 8 karakter
        if (strlen($password) < 8) return false;
        
        // Harus mengandung huruf
        if (!preg_match('/[a-zA-Z]/', $password)) return false;
        
        // Harus mengandung angka
        if (!preg_match('/[0-9]/', $password)) return false;
        
        // Harus mengandung simbol
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) return false;
        
        return true;
    }
}

if (!function_exists('registerUser')) {
    function registerUser($username, $password) {
        global $pdo;
        
        // Validasi username
        if (strlen($username) > 15) {
            return "Username tidak boleh lebih dari 15 karakter!";
        }
        
        // Validasi password
        if (!validatePassword($password)) {
            return "Password harus minimal 8 karakter dan mengandung kombinasi huruf, angka, dan simbol!";
        }
        
        try {
            // Cek apakah username sudah ada
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                return "Username sudah terdaftar!";
            }
            
            // Generate salt dan hash password
            $salt = generateSalt();
            $hashedPassword = hashPassword($password, $salt);
            
            // Simpan ke database
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, salt) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $salt]);
            
            return "Registrasi berhasil!";
        } catch(PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

if (!function_exists('authenticateUser')) {
    function authenticateUser($username, $password) {
        global $pdo;
        
        try {
            // Ambil data user dari database
            $stmt = $pdo->prepare("SELECT password_hash, salt FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() == 0) {
                return "Username tidak ditemukan!";
            }
            
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifikasi password
            $hashedPassword = hashPassword($password, $userData['salt']);
            
            if ($hashedPassword === $userData['password_hash']) {
                return "Login berhasil!";
            } else {
                return "Password salah!";
            }
        } catch(PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
?>