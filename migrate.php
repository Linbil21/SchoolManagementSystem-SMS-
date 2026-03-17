<?php
/**
 * DATABASE MIGRATION TOOL
 * Run this via browser: http://localhost/sms/migrate.php
 */

require_once 'Database/config.php';

echo "<h2>Starting Database Migration...</h2>";

try {
    // 1. Ensure 'passport' column exists in 'enrollments'
    echo "Checking 'passport' column in 'enrollments' table...<br>";
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS passport VARCHAR(255) NULL AFTER form_138");
    echo "✅ Passport column is ready.<br><br>";

    // 2. Ensure 'misc_fee' and 'tuition_fee' exist and have correct types
    echo "Checking fee columns...<br>";
    $pdo->exec("ALTER TABLE enrollments MODIFY COLUMN tuition_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments MODIFY COLUMN misc_fee DECIMAL(10,2) DEFAULT 4975.00");
    echo "✅ Fee columns are optimized.<br><br>";

    // 3. Update existing assessment records to the new 4,975 model if they are still 0 or empty
    echo "Updating existing assessment balances...<br>";
    $pdo->exec("UPDATE enrollments SET misc_fee = 4975.00, tuition_fee = 0.00, total_fee = 4975.00, balance = 4975.00 WHERE (total_fee IS NULL OR total_fee = 0) AND status = 'Pending Payment'");
    echo "✅ Existing assessments synced.<br><br>";

    echo "<h2 style='color: green;'>Migration Completed Successfully!</h2>";
    echo "<p><a href='student/Dashboard.php'>Return to Dashboard</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Migration Failed!</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
