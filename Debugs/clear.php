<?php
require_once 'Database/config.php';

try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // Clear the main enrollment and student tables
    $pdo->exec('TRUNCATE TABLE enrollments');
    $pdo->exec('TRUNCATE TABLE students');
    $pdo->exec('TRUNCATE TABLE admission_applications');
    
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    echo "<h1>Data Cleared Successfully!</h1>";
    echo "<p>All dummy data in enrollments, students, and admission_applications has been cleared.</p>";
    echo "<p><a href='auth/Login.php?action=register'>Go back to Enrollment Form</a></p>";
} catch (PDOException $e) {
    echo "<h1>Error Clearing Data:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
