<?php
require 'c:\Users\Linbil Celestre\Desktop\sms\Database\config.php';
try {
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS passport VARCHAR(255) NULL AFTER form_138");
    echo "Database Updated Successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
unlink(__FILE__);
?>
