<?php
require 'Database/config.php';
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute(['Cashier@example.com']);
$user = $stmt->fetch();
if ($user) {
    echo "Found user:\n";
    print_r($user);
} else {
    echo "User not found. Checking all users:\n";
    $stmt = $pdo->query('SELECT userId, email, role FROM users');
    print_r($stmt->fetchAll());
}
?>
