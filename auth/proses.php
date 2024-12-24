<?php
session_start();
include '../config/koneksi.php';

// Proses Login
if(isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    
    if(mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect berdasarkan role
            if($user['role'] == 'admin') {
                header("Location: ../pages/peta/peta.php");
            } else {
                header("Location: ../pages/user/user.php");
            }
            exit;
        }
    }
    
    header("Location: login.php?error=Username atau password salah!");
    exit;
}

// Proses Register
if(isset($_POST['register'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $role = 'user'; // Set default role sebagai user
    
    // Validasi password
    if($password !== $konfirmasi_password) {
        header("Location: register.php?error=Password tidak cocok!");
        exit;
    }
    
    // Cek username
    $check_username = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if(mysqli_num_rows($check_username) > 0) {
        header("Location: register.php?error=Username sudah digunakan!");
        exit;
    }
    
    // Cek email
    $check_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if(mysqli_num_rows($check_email) > 0) {
        header("Location: register.php?error=Email sudah digunakan!");
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru dengan role
    $query = mysqli_query($koneksi, "INSERT INTO users (nama_lengkap, email, username, password, role) 
                                    VALUES ('$nama_lengkap', '$email', '$username', '$hashed_password', '$role')");
    
    if($query) {
        header("Location: login.php?success=Registrasi berhasil! Silakan login.");
        exit;
    } else {
        header("Location: register.php?error=Registrasi gagal!");
        exit;
    }
}
