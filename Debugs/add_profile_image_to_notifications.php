<?php
require_once '../Database/config.php';
try {
    // Add profile_image column to notifications table
    $pdo->exec("ALTER TABLE notifications ADD COLUMN profile_image VARCHAR(255) NULL AFTER message");
    echo "✅ Added profile_image column to notifications table\n";
    
    // Verify
    $cols = $pdo->query("DESCRIBE notifications")->fetchAll(PDO::FETCH_ASSOC);
    echo "\n📋 Updated Table Structure:\n";
    foreach ($cols as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️  Column 'profile_image' already exists\n";
    } else {
        echo "❌ Error: " . $e->getMessage();
    }
}
?>
