<?php
require_once 'Database/config.php';
echo "--- Enrollments Table ---\n";
$stmt = $pdo->query("DESCRIBE enrollments");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Primary Key for enrollments: " . ($columns[0]['Field'] ?? 'Unknown') . "\n";
print_r(array_slice($columns, 0, 5));

echo "\n--- Payments Table ---\n";
$stmt = $pdo->query("DESCRIBE payments");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
