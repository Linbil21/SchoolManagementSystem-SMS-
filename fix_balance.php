<?php
/**
 * ONE-CLICK BALANCE RESET
 * This script will force Lowell's balance to exactly ₱4,975.00
 */
require_once 'Database/config.php';

echo "<h2>Force Fixing Balance...</h2>";

try {
    // 1. Find Lowell's Email
    $stmt = $pdo->query("SELECT email, first_name, last_name FROM students WHERE first_name LIKE '%Lowell%' OR last_name LIKE '%Toribio%' LIMIT 1");
    $student = $stmt->fetch();

    if ($student) {
        $email = $student->email;
        echo "Found Student: <b>" . $student->first_name . " " . $student->last_name . "</b> ($email)<br>";

        // 2. Force Update Enrollment Record
        $update = $pdo->prepare("
            UPDATE enrollments 
            SET tuition_fee = 0.00, 
                misc_fee = 4975.00, 
                total_fee = 4975.00, 
                balance = 4975.00,
                status = 'Pending Payment'
            WHERE email = ?
        ");
        $update->execute([$email]);

        // 3. Optional: Delete phantom/ghost payments that cause negative balance
        // (If we want to be safe, we just update the balance as requested)
        
        echo "<h2 style='color: green;'>✅ SUCCESS! Balance is now ₱4,975.00</h2>";
        echo "<p>Please visit this link now: <a href='student/Modules/Payments/Balance.php'>Go to Balance Page</a></p>";
    } else {
        echo "<h2 style='color: red;'>❌ Student 'Lowell' not found in database.</h2>";
    }

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error: " . $e->getMessage() . "</h2>";
}
?>
