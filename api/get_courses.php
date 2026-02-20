<?php
require_once '../Database/config.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT courseId as course_id, course_name FROM courses ORDER BY course_name ASC");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'data' => $courses
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
