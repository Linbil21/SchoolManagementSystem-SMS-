<?php
require_once 'Database/config.php';

try {
    // Check students table columns
    echo "--- Students Table Columns ---\n";
    $stmt = $pdo->query("DESCRIBE students");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo $c['Field'] . " (" . $c['Type'] . ")\n";
    }

    // Check courses table exists and columns
    echo "\n--- Courses Table Columns ---\n";
    $stmt = $pdo->query("DESCRIBE courses");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo $c['Field'] . " (" . $c['Type'] . ")\n";
    }
    
    // Try the join query manually and see error
    echo "\n--- Testing Join Query ---\n";
    $sql = "
        SELECT 
            s.student_id, 
            s.first_name, 
            s.last_name
        FROM students s 
        LEFT JOIN courses c ON s.course_id = c.courseId 
        LIMIT 1
    ";
    $stmt = $pdo->query($sql);
    echo "Query Successful!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
