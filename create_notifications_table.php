<?php
require_once 'Database/config.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents('Database/notifications_table.sql');
    $pdo->exec($sql);
    echo "✅ Notifications table created successfully!\n";
    
    // Verify it was created
    $tables = $pdo->query("SHOW TABLES LIKE 'notifications'")->fetchAll();
    if (count($tables) > 0) {
        echo "✅ Table verification: notifications table exists\n";
        
        // Show structure
        $cols = $pdo->query("DESCRIBE notifications")->fetchAll(PDO::FETCH_ASSOC);
        echo "\n📋 Table Structure:\n";
        foreach ($cols as $col) {
            echo "  - {$col['Field']} ({$col['Type']})\n";
        }
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
