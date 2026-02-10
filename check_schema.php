<?php
require_once 'Database/config.php';
$stmt = $pdo->query("DESCRIBE payments");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
