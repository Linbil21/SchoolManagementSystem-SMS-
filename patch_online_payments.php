<?php
$file = 'c:/xampp/htdocs/sms/Cashier/Modules/Online-Payments.php';
$content = file_get_contents($file);
$content = str_replace(
    "currentPaymentId = data.payment_id;",
    "currentPaymentId = data.payment_id;\n            document.getElementById('modalPurposeDetails').textContent = data.purpose || 'None provided';",
    $content
);
file_put_contents($file, $content);
echo "File updated successfully.";
?>
