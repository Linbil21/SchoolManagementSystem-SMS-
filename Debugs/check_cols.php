<?php
require_once 'Database/config.php';
try {
    foreach(['enrollments', 'notifications', 'users'] as $t) {
        $stmt = $pdo->query("DESCRIBE $t");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "$t: " . implode(', ', $cols) . "\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
