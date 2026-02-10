<?php
$file = 'c:/xampp/htdocs/sms/Cashier/Modules/Uploaded-Receipts.php';
$content = file_get_contents($file);

// Update JS function signature and body
$content = str_replace(
    "function openVerifyModal(name, ref, amount, method, img, id) {",
    "function openVerifyModal(name, ref, amount, method, img, id, purpose) {",
    $content
);
$content = str_replace(
    "document.getElementById('modalMethod').textContent = method;",
    "document.getElementById('modalMethod').textContent = method;\n            document.getElementById('modalPurpose').textContent = purpose || 'None provided';",
    $content
);

// Update PHP loop - this is trickier due to dynamic content, I'll use a regex
$pattern = '/\$img = "\/sms\/" \. htmlspecialchars\(\$row->proof_of_payment\);\s+echo "<tr>/s';
$replacement = '$img = "/sms/" . htmlspecialchars($row->proof_of_payment);' . "\n                                    " . '$purpose = htmlspecialchars($row->purpose ?? "");' . "\n                                    " . 'echo "<tr>';
$content = preg_replace($pattern, $replacement, $content);

// Update the onclick button
$content = str_replace(
    "onclick=\"openVerifyModal('\$name', '\$ref', '₱\$amount', '\$method', '\$img', '{\$row->payment_id}')\"",
    "onclick=\"openVerifyModal('\$name', '\$ref', '₱\$amount', '\$method', '\$img', '{\$row->payment_id}', '\$purpose')\"",
    $content
);

file_put_contents($file, $content);
echo "File updated successfully.";
?>
