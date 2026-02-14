<?php
require_once '../Database/config.php';
try {
    $stmt = $pdo->query("DESCRIBE enrollments");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cols, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
