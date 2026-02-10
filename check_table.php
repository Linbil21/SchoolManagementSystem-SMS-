<?php
require_once 'Database/config.php';
$stmt = $pdo->query("DESCRIBE payments");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($columns);
?>
