<?php
require_once 'Database/config.php';
$stmt = $pdo->query("DESCRIBE notifications");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
