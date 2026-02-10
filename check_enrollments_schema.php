<?php
require_once 'Database/config.php';
try {
    $cols = $pdo->query("DESCRIBE enrollments")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cols, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
