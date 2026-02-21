<?php
require_once 'Database/config.php';
try {
    $st = $pdo->query('SELECT email, status, reference_code FROM enrollments ORDER BY created_at DESC LIMIT 5');
    print_r($st->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {}
?>
