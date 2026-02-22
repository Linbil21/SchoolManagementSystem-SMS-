<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->prepare("SELECT e.enrollmentId, e.first_name, e.last_name, p.proof_of_payment 
                           FROM enrollments e 
                           LEFT JOIN payments p ON e.enrollmentId = p.enrollment_id 
                           WHERE e.last_name LIKE '%TORIBIO%'");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) { echo $e->getMessage(); }
