<?php
require_once '../Database/config.php';
try {
    $stmt = $pdo->query("SELECT * FROM courses");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($courses, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
