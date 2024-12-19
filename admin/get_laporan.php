<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    
    $query = "SELECT l.*, u.nama FROM laporan l JOIN users u ON l.user_id = u.id";
    if ($status) {
        $query .= " WHERE l.status = :status";
    }
    $query .= " ORDER BY l.created_at DESC";
    
    $stmt = $conn->prepare($query);
    if ($status) {
        $stmt->bindParam(':status', $status);
    }
    $stmt->execute();
    $laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($laporan);
} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error']);
}
?>
