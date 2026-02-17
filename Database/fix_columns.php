<?php
require_once 'config.php';

try {
    // 1. Add missing columns to students table
    $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS verification_code VARCHAR(10) DEFAULT NULL AFTER profile_image");
    $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0 AFTER verification_code");
    
    // 2. Add missing columns to admission_applications if they don't exist
    // Just to be safe, though most are already there
    
    echo "<h1>Database Update Success!</h1>";
    echo "<p>Missing columns added to 'students' table.</p>";
    echo "<a href='../auth/Login.php?action=register'>Try Registration Again</a>";

} catch (PDOException $e) {
    echo "<h1>Database Update Failed</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
