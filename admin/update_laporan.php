<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $tindak_lanjut = $_POST['tindak_lanjut'];

    $stmt = $conn->prepare("UPDATE laporan SET status = ?, tindak_lanjut = ? WHERE id = ?");
    $stmt->execute([$status, $tindak_lanjut, $id]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
