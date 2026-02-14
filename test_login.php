<?php
session_start();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'Cashier@example.com';
$_POST['password'] = 'cashier123';
$_POST['ajax'] = '1';
$_POST['csrf_token'] = 'test';
$_SESSION['csrf_token'] = 'test';

chdir('auth');
ob_start();
include 'auth_process.php';
$output = ob_get_clean();

echo "Login Response:\n";
echo $output;
echo "\n\nSession Data:\n";
print_r($_SESSION);
?>
