<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $geojson = $_POST['geojson'];

    if ($id) {
        // Update existing wilayah
        $stmt = $conn->prepare("UPDATE wilayah SET nama = ?, deskripsi = ?, geojson = ? WHERE id = ?");
        $stmt->execute([$nama, $deskripsi, $geojson, $id]);
    } else {
        // Insert new wilayah
        $stmt = $conn->prepare("INSERT INTO wilayah (nama, deskripsi, geojson) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $deskripsi, $geojson]);
    }

    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
