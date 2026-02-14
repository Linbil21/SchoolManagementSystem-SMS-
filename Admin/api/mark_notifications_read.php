<?php
session_start();
require_once '../../Database/config.php';

// Check if authorized role
$allowed_roles = ['admin', 'superadmin', 'admission', 'cashier'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    // Mark all notifications as read
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
