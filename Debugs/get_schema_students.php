<?php
// Minimal config bypass
$pdo = new PDO("mysql:host=localhost;dbname=sms", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $stmt = $pdo->query("DESCRIBE students");
    echo "students:\n";
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
        echo "  {$c['Field']} ({$c['Type']})\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
