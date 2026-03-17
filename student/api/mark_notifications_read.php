<?php
require_once '../../Database/config.php';
session_start();

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/student/', '/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/modules/'];
$project_base = '';
foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/';

// Check if student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $notif_id = $_REQUEST['notification_id'] ?? null;

    if ($notif_id) {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$notif_id]);
        echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
    } else {
        // Mark all notifications as read
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
