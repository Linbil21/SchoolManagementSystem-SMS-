<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM enrollments GROUP BY status");
    $enrollment_stats = $stmt->fetchAll();
    echo "Enrollment Statuses:\n";
    print_r($enrollment_stats);

    $stmt = $pdo->query("SELECT is_verified, COUNT(*) as count FROM students GROUP BY is_verified");
    $verification_stats = $stmt->fetchAll();
    echo "\nVerification Statuses:\n";
    print_r($verification_stats);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
