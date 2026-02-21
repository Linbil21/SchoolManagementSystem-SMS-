<?php
session_start();
require_once '../Database/config.php';

// 1. Update Staff/Admin status to offline
if (isset($_SESSION['userId'])) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET status = 'offline' WHERE userId = ?");
        $stmt->execute([$_SESSION['userId']]);
    } catch (PDOException $e) {}
}

// 2. Update Student status to offline
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE students SET status = 'offline' WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (PDOException $e) {}
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: Login.php");
exit();
?>