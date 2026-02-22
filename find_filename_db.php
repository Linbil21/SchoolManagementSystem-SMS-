<?php
require_once 'Database/config.php';
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($cols as $col) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE `$col` LIKE '%sample_gcash.jpg%'");
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                echo "Found in $table.$col\n";
                $data = $pdo->query("SELECT `$col` FROM $table WHERE `$col` LIKE '%sample_gcash.jpg%' LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);
                print_r($data);
            }
        }
    }
} catch (Exception $e) { echo $e->getMessage(); }
