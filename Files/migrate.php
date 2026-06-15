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

    // 2. Fix Swapped Fees (Lowell's case: Tuition was 4975, Misc was 0)
    echo "Fixing swapped fee values (Tuition vs Miscellaneous)...<br>";
    $pdo->exec("UPDATE enrollments SET misc_fee = 4975.00, tuition_fee = 0.00 WHERE tuition_fee = 4975.00 AND misc_fee = 0.00");
    $pdo->exec("UPDATE enrollments SET total_fee = tuition_fee + misc_fee + IFNULL(lab_fee, 0)");
    echo "✅ Fee values synchronized.<br><br>";

    // 3. Recalculate Balances for everyone accurately
    echo "Recalculating all student balances based on payments...<br>";
    $enrollments = $pdo->query("SELECT enrollmentId, email, total_fee FROM enrollments")->fetchAll();
    
    foreach ($enrollments as $enr) {
        // Sum verified payments
        $stmt = $pdo->prepare("SELECT SUM(amount) as paid FROM payments WHERE (enrollment_id = ? OR (enrollment_id IS NULL AND description LIKE ?)) AND status IN ('Completed', 'Verified', 'Success')");
        $stmt->execute([$enr->enrollmentId, "%" . $enr->email . "%"]);
        $total_paid = $stmt->fetch()->paid ?? 0;
        
        $new_balance = $enr->total_fee - $total_paid;
        
        $update = $pdo->prepare("UPDATE enrollments SET balance = ? WHERE enrollmentId = ?");
        $update->execute([$new_balance, $enr->enrollmentId]);
    }
    echo "✅ All balances recalculated.<br><br>";

    echo "<h2 style='color: green;'>Migration Completed Successfully!</h2>";
    echo "<p>Please refresh your <a href='student/Modules/Payments/Balance.php'>Balance Page</a> now.</p>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Migration Failed!</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
