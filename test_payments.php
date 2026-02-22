<?php
require_once 'Database/config.php';
$stmt = $pdo->query("SELECT * FROM payments ORDER BY created_at DESC LIMIT 5");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
