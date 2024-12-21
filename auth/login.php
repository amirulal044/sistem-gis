<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Cek username dan password jika login sebagai admin
        if ($username == 'admin' && $password == 'admin123') {
            // Login berhasil untuk admin tanpa perlu mengecek database
            $_SESSION['user_id'] = 1;  // ID admin (sesuaikan dengan ID di database)
            $_SESSION['username'] = 'admin';
            $_SESSION['role'] = 'admin';

            // Redirect ke halaman dashboard admin
            header("Location: ../admin/dashboard.php");
            exit();
        }

        // Jika bukan admin, lakukan pengecekan dengan database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Debug: Cek apakah user ditemukan
        if ($user) {
            echo "User ditemukan: " . $user['username'] . "<br>";
        } else {
            echo "User tidak ditemukan<br>";
        }

        // Cek password dengan password_verify
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Debug: Cek role
            echo "Role user: " . $user['role'] . "<br>";

            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../warga/dashboard.php");
            }
            exit();
        } else {
            echo "Password salah<br>";
            header("Location: ../index.php?error=Username atau password salah");
            exit();
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();  // Debugging
        header("Location: ../index.php?error=Terjadi kesalahan sistem");
        exit();
    }
}
?>
