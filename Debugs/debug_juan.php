<?php
require_once '../Database/config.php';
try {
    $stmt = $pdo->prepare("SELECT student_id, first_name, last_name, profile_image FROM students WHERE first_name LIKE '%juan%'");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
