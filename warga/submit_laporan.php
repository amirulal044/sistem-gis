<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warga') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    // Handle file upload
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $foto = $_FILES['foto'];
    $imageFileType = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;

    // Check if image file is actual image
    $check = getimagesize($foto["tmp_name"]);
    if ($check === false) {
        throw new Exception("File bukan gambar yang valid");
    }

    // Check file size (max 5MB)
    if ($foto["size"] > 5000000) {
        throw new Exception("Ukuran file terlalu besar (maksimal 5MB)");
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        throw new Exception("Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan");
    }

    if (!move_uploaded_file($foto["tmp_name"], $target_file)) {
        throw new Exception("Gagal mengupload file");
    }

    // Save to database
    $stmt = $conn->prepare("INSERT INTO laporan (user_id, judul, deskripsi, foto, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['judul'],
        $_POST['deskripsi'],
        $newFileName,
        $_POST['latitude'],
        $_POST['longitude']
    ]);

    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
