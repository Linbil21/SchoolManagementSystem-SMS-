<?php
require_once 'Database/config.php';
try {
    $pdo->exec("ALTER TABLE payments ADD COLUMN purpose VARCHAR(255) AFTER description");
    echo "Column 'purpose' added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
