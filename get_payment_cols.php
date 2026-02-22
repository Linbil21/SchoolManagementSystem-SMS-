<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM payments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
