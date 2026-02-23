<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("DESCRIBE admission_applications");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
} catch (Exception $e) { echo $e->getMessage(); }
