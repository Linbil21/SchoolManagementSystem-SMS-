<?php
// Force define credentials since CLI lacks $_SERVER context
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sms');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS passport VARCHAR(255) NULL AFTER form_138");
    echo "Database Updated Successfully (Passport Column Added)";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
// Do not unlink yet so I can verify
?>
