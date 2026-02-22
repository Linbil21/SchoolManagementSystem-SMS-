<?php
require_once 'Database/config.php';
$stmt = $pdo->query("SELECT * FROM enrollments WHERE email LIKE '%lowell%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
