<?php
require_once '../Database/config.php';
$stmt = $pdo->query("DESCRIBE payments");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo implode(", ", $columns);
?>
