<?php
require_once 'Database/config.php';
$stmt = $pdo->query('SHOW COLUMNS FROM courses');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
