<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM payments");
    echo "COLUMNS IN payments:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Table 'payments' does not exist. Creating it...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        enrollment_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        transaction_id VARCHAR(100) NOT NULL,
        status VARCHAR(20) DEFAULT 'Completed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'payments' created.\n";
}
?>
