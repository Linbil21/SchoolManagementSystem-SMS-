<?php
require_once '../Database/config.php';
$stmt = $pdo->query("DESCRIBE students");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($cols);
?>
