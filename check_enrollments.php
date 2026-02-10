<?php
require_once 'Database/config.php';
$stmt = $pdo->query("DESCRIBE enrollments");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($cols);
?>
