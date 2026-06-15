<?php
require 'c:/xampp/htdocs/sms/Database/config.php';
$stmt = $pdo->query('SELECT * FROM students');
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($students, JSON_PRETTY_PRINT);
?>
