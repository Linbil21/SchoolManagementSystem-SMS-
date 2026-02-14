<?php
require_once 'Database/config.php';

try {
    $stmt = $pdo->query("SELECT student_id, first_name, last_name, profile_image FROM students WHERE student_id = '2024-0001'");
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Student ID: " . $student['student_id'] . "\n";
    echo "Name: " . $student['first_name'] . " " . $student['last_name'] . "\n";
    echo "Profile Image: [" . $student['profile_image'] . "]\n";
    
    // Check file existence via PHP relative to current directory (sms)
    $path = $student['profile_image'];
    if (empty($path)) {
        echo "Profile image is empty.\n";
    } else {
        echo "Profile image is NOT empty.\n";
        if (file_exists($path)) {
            echo "File exists at : " . realpath($path) . "\n";
            echo "YES, FILE EXISTS\n";
        } else {
            echo "File DOES NOT exist at: " . $path . "\n";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
