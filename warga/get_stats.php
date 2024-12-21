<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warga') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    // Get total reports
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM laporan WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total = $stmt->fetch()['total'];

    // Get in process reports
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM laporan WHERE user_id = ? AND status = 'proses'");
    $stmt->execute([$_SESSION['user_id']]);
    $proses = $stmt->fetch()['total'];

    // Get completed reports
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM laporan WHERE user_id = ? AND status = 'selesai'");
    $stmt->execute([$_SESSION['user_id']]);
    $selesai = $stmt->fetch()['total'];

    $stats = [
        'total' => $total,
        'proses' => $proses,
        'selesai' => $selesai
    ];

    header('Content-Type: application/json');
    echo json_encode($stats);
} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error']);
}
?>
