<?php
require_once 'Database/config.php';

try {
    $pdo->beginTransaction();
    
    // Fetch all enrollments
    $stmt = $pdo->query("SELECT enrollmentId, email, total_fee FROM enrollments");
    $enrollments = $stmt->fetchAll();
    
    $updated = 0;
    foreach ($enrollments as $enr) {
        $eid = $enr->enrollmentId;
        $email = $enr->email;
        $total = $enr->total_fee;
        
        // Sum all 'Completed' or 'Verified' payments for this enrollment OR email (if orphaned)
        $paid_stmt = $pdo->prepare("SELECT SUM(amount) as paid FROM payments WHERE (enrollment_id = ? OR (enrollment_id IS NULL AND description LIKE ?)) AND status IN ('Completed', 'Verified')");
        $paid_stmt->execute([$eid, "%" . $email . "%"]);
        $total_paid = $paid_stmt->fetch()->paid ?? 0;
        
        $new_balance = $total - $total_paid;
        
        // Update the balance
        $upd = $pdo->prepare("UPDATE enrollments SET balance = ? WHERE enrollmentId = ?");
        $upd->execute([$new_balance, $eid]);
        
        // Also link any orphaned payments
        $pdo->prepare("UPDATE payments SET enrollment_id = ? WHERE description LIKE ? AND enrollment_id IS NULL")->execute([$eid, "%" . $email . "%"]);
        
        $updated++;
    }
    
    $pdo->commit();
    echo "Successfully updated balances for $updated enrollment records.";
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
