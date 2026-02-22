<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("DESCRIBE enrollments");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($fields, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
