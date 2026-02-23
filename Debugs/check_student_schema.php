<?php
require_once 'Database/config.php';
try {
    foreach(['students', 'enrollments'] as $t) {
        $stmt = $pdo->query("DESCRIBE $t");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "$t:\n";
        foreach($cols as $c) {
            echo "  {$c['Field']} ({$c['Type']})\n";
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
