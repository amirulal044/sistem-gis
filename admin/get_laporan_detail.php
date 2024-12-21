<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT l.*, u.nama FROM laporan l JOIN users u ON l.user_id = u.id WHERE l.id = ?");
    $stmt->execute([$id]);
    $laporan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($laporan);
} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error']);
}
?>
