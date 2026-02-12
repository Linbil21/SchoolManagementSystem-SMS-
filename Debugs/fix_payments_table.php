<?php
require_once __DIR__ . '/../Database/config.php';
try {
    $columnsToAdd = [
        "proof_of_payment VARCHAR(255) NULL AFTER status",
        "transaction_id VARCHAR(100) NULL AFTER proof_of_payment",
        "payment_date DATE NULL AFTER transaction_id",
        "purpose VARCHAR(255) NULL AFTER payment_date",
        "semester VARCHAR(50) NULL AFTER purpose",
        "description TEXT NULL AFTER semester"
    ];

    foreach ($columnsToAdd as $col) {
        try {
            $pdo->exec("ALTER TABLE payments ADD COLUMN $col");
            echo "Success: Added column " . explode(' ', trim($col))[0] . "<br>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "Info: Column " . explode(' ', trim($col))[0] . " already exists.<br>";
            } else {
                throw $e;
            }
        }
    }
    echo "Database patch completed.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
