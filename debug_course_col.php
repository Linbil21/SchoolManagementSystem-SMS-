<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("SELECT course FROM students LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $r) {
        echo "Course: " . $r['course'] . "\n";
    }
} catch (PDOException $e) { echo $e->getMessage(); }
?>
