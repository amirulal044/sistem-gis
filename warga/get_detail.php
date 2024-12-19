<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warga') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM laporan WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $laporan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$laporan) {
        throw new Exception('Laporan tidak ditemukan');
    }
    
    header('Content-Type: application/json');
    echo json_encode($laporan);
} catch(Exception $e) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => $e->getMessage()]);
}
?>
