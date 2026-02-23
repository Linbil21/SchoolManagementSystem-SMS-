<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments");
    echo "Enrollments:\n" . json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT) . "\n\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM notifications");
    echo "Notifications:\n" . json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT) . "\n\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    echo "Users:\n" . json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
