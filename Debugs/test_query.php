<?php
session_start();
require_once 'Database/config.php';

try {
    $stmt = $pdo->prepare("SELECT email FROM enrollments LIMIT 1");
    $stmt->execute();
    $test = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Success: " . json_encode($test) . "\n";
} catch (Exception $e) {
    echo "Enrollments error: " . $e->getMessage() . "\n";
}

try {
    $stmt = $pdo->prepare("SELECT enrollmentId, balance, first_name, last_name FROM enrollments ORDER BY created_at DESC LIMIT 1");
    $stmt->execute();
    $test2 = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Success2: " . json_encode($test2) . "\n";
} catch (Exception $e) {
    echo "Gateway Query error: " . $e->getMessage() . "\n";
}
?>
