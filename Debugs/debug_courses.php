<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("SELECT * FROM courses");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $r) {
        print_r($r);
    }
} catch (PDOException $e) { echo $e->getMessage(); }
?>
