<?php
require_once '../Database/config.php';
try {
    $pdo->exec("ALTER TABLE payments ADD COLUMN description TEXT NULL AFTER payment_method");
    $pdo->exec("ALTER TABLE payments ADD COLUMN semester VARCHAR(50) NULL AFTER description");
    echo "Success: Table altered.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Info: Columns already exist.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
