<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    // Get total wilayah
    $stmt = $conn->query("SELECT COUNT(*) as total FROM wilayah");
    $total_wilayah = $stmt->fetch()['total'];

    // Get pending reports
    $stmt = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE status = 'pending'");
    $laporan_pending = $stmt->fetch()['total'];

    // Get total reports
    $stmt = $conn->query("SELECT COUNT(*) as total FROM laporan");
    $total_laporan = $stmt->fetch()['total'];

    $stats = [
        'total_wilayah' => $total_wilayah,
        'laporan_pending' => $laporan_pending,
        'total_laporan' => $total_laporan
    ];

    header('Content-Type: application/json');
    echo json_encode($stats);
} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error']);
}
?>
