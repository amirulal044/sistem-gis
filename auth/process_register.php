<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        // Validasi input
        if (empty($nama) || empty($username) || empty($password)) {
            throw new Exception("Semua field harus diisi");
        }

        if ($password !== $confirm_password) {
            throw new Exception("Password tidak cocok");
        }

        if (strlen($password) < 6) {
            throw new Exception("Password minimal 6 karakter");
        }

        // Cek username sudah digunakan atau belum
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception("Username sudah digunakan");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user baru
        $stmt = $conn->prepare("INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, 'warga')");
        $stmt->execute([$username, $hashed_password, $nama]);

        header("Location: ../register.php?success=1");
        exit();
    } catch(Exception $e) {
        header("Location: ../register.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>
