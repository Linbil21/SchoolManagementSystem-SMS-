<?php
require_once 'Database/config.php';

try {
    $stmt = $pdo->query("SELECT student_id, first_name, last_name, profile_image FROM students");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total Students Found: " . count($students) . "\n";
    foreach ($students as $s) {
        echo "ID: " . $s['student_id'] . " | Name: " . $s['first_name'] . " " . $s['last_name'] . " | Image: [" . $s['profile_image'] . "]\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
