<?php
require_once 'Database/config.php';

try {
    $stmt = $pdo->prepare("SELECT email FROM enrollments WHERE first_name LIKE '%LOWELL%'");
    $stmt->execute();
    $test = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Enrollments Emails: " . json_encode($test) . "\n";
} catch (Exception $e) {}

try {
    $stmt = $pdo->prepare("SELECT email FROM students WHERE first_name LIKE '%LOWELL%'");
    $stmt->execute();
    $test2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Students Emails: " . json_encode($test2) . "\n";
} catch (Exception $e) {}
?>
