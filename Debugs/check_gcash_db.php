<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("SELECT proof_of_payment FROM payments WHERE proof_of_payment LIKE '%gcash%' LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Gcash Path: " . ($row['proof_of_payment'] ?? 'NOT FOUND') . "\n";
} catch (Exception $e) { echo $e->getMessage(); }
