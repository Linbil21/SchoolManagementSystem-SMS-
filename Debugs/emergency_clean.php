<?php
// Emergency Fix v2: Aggressively clean stray CSS from ALL PHP files
header('Content-Type: text/plain; charset=utf-8');

$strayText = ".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }";

// Target ONLY these specific files first
$targetFiles = [
    __DIR__ . '/Admission/Modules/New-Applications.php',
    __DIR__ . '/Admission/Components/Sidebar.php',
    __DIR__ . '/Admission/Components/header.php',
    __DIR__ . '/Admission/Components/GlobalScripts.php',
    __DIR__ . '/Admission/Components/GlobalModal.php',
];

echo "=== EMERGENCY CLEAN v2 ===\n\n";
echo "Target: \"$strayText\"\n\n";

$totalFixed = 0;

foreach ($targetFiles as $file) {
    if (!file_exists($file)) {
        echo "SKIP (not found): " . basename($file) . "\n";
        continue;
    }

    $content = file_get_contents($file);
    $count = substr_count($content, $strayText);

    if ($count > 0) {
        $newContent = str_replace($strayText, '', $content);
        $newContent = str_replace(trim($strayText), '', $newContent);
        file_put_contents($file, $newContent);
        $totalFixed++;
        echo "FIXED ($count occurrence): " . basename($file) . "\n";
    } else {
        echo "Clean: " . basename($file) . "\n";
    }
}

echo "\n--- Total files fixed: $totalFixed ---\n\n";

// Show raw content of New-Applications.php body area
$naFile = __DIR__ . '/Admission/Modules/New-Applications.php';
if (file_exists($naFile)) {
    $lines = file($naFile);
    echo "=== New-Applications.php (lines with relevant content) ===\n";
    foreach ($lines as $i => $line) {
        if (stripos($line, '<body') !== false || 
            stripos($line, 'student-modal-footer') !== false ||
            stripos($line, 'padding: 20px') !== false) {
            echo "Line " . ($i+1) . ": " . rtrim($line) . "\n";
        }
    }
}

// OPCache clear
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "\nOPCache cleared!\n";
}

echo "\nDONE. Hard reload (Ctrl+Shift+R) the page now.\n";
?>
