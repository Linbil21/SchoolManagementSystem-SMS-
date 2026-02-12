<?php
require_once '../Database/config.php';
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(", ", $tables) . "\n\n";
    
    // Check if notifications table exists
    if (in_array('notifications', $tables)) {
        echo "Notifications table exists!\n";
        $cols = $pdo->query("DESCRIBE notifications")->fetchAll(PDO::FETCH_COLUMN);
        echo "Columns: " . implode(", ", $cols);
    } else {
        echo "Notifications table does NOT exist. Need to create it.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
