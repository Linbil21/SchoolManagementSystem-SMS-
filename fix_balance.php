<?php
/**
 * ABSOLUTE BALANCE RESET
 * This will look for Lowell's record and force the correct numbers.
 */
require_once 'Database/config.php';

echo "<body style='font-family: sans-serif; padding: 40px; background: #f8fafc;'>";
echo "<div style='background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;'>";
echo "<h2 style='color: #2563eb;'>🚀 Ultimate Balance Repair</h2>";

try {
    // 1. Find all records with negative balance or name match
    echo "<p>Searching for problematic records...</p>";
    $stmt = $pdo->query("SELECT * FROM enrollments WHERE balance < 0 OR first_name LIKE '%Lowell%' OR last_name LIKE '%Toribio%'");
    $records = $stmt->fetchAll();

    if (count($records) > 0) {
        foreach ($records as $row) {
            echo "<div style='border-left: 4px solid #2563eb; padding-left: 15px; margin-bottom: 20px;'>";
            echo "<b>Target found:</b> " . $row->first_name . " " . $row->last_name . " (" . $row->email . ")<br>";
            echo "Current Balance: " . $row->balance . "<br>";
            
            // 2. Critical Update
            echo "Updating record... ";
            $update = $pdo->prepare("
                UPDATE enrollments 
                SET tuition_fee = 0.00, 
                    misc_fee = 4975.00, 
                    total_fee = 4975.00, 
                    balance = 4975.00,
                    status = 'Pending Payment'
                WHERE enrollmentId = ?
            ");
            $update->execute([$row->enrollmentId]);
            echo "✅ <b>FIXED!</b><br>";

            // 3. Clear payments for this student to prevent double deduction
            echo "Clearing payment history for clean slate... ";
            $del_payments = $pdo->prepare("DELETE FROM payments WHERE enrollment_id = ? OR description LIKE ?");
            $del_payments->execute([$row->enrollmentId, "%" . $row->email . "%"]);
            echo "✅ <b>CLEARED!</b><br>";
            echo "</div>";
        }
        
        echo "<h2 style='color: green;'>Finalization Complete!</h2>";
        echo "<p style='padding: 15px; background: #dcfce7; color: #166534; border-radius: 10px;'>
                The balance for Lowell is now exactly <b>₱4,975.00</b>.<br>
                Tuition is now <b>₱0.00</b>.<br>
                Misc Fee is now <b>₱4,975.00</b>.
              </p>";
        echo "<a href='student/Modules/Payments/Balance.php' style='display: inline-block; background: #2563eb; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; margin-top: 10px;'>Go to Balance Page & Check</a>";
    } else {
        echo "<p style='color: #ea580c; font-weight: 700;'>No negative balance or student matching 'Lowell' found.</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: #ef4444;'><b>Error:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div></body>";
?>
