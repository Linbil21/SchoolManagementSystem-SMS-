<?php
/**
 * FIX STRAY CSS - v3 Final
 * Directly patches New-Applications.php body on the live server
 * DELETE AFTER USE
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== FIX STRAY CSS v3 ===\n\n";

$strayText = ".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }";

$filesToCheck = [
    __DIR__ . '/Admission/Modules/New-Applications.php',
    __DIR__ . '/Admission/Components/Sidebar.php',
    __DIR__ . '/Admission/Components/header.php',
    __DIR__ . '/Admission/Components/GlobalScripts.php',
    __DIR__ . '/Admission/Components/GlobalModal.php',
];

$totalFixed = 0;

foreach ($filesToCheck as $file) {
    if (!file_exists($file)) {
        echo "SKIP: " . basename($file) . " (not found)\n";
        continue;
    }

    $content = file_get_contents($file);

    // Check all possible stray patterns
    $patterns = [
        "<body>\n" . $strayText,
        "<body>\r\n" . $strayText,
        "<body>" . $strayText,
        "\n" . $strayText . "\n",
        "\r\n" . $strayText . "\r\n",
        " " . $strayText,
        $strayText,
    ];

    $changed = false;
    foreach ($patterns as $pattern) {
        if (strpos($content, $pattern) !== false) {
            // Replace pattern but keep the <body> tag if it was included
            if (strpos($pattern, '<body>') === 0) {
                $content = str_replace($pattern, '<body>', $content);
            } else {
                $content = str_replace($pattern, '', $content);
            }
            $changed = true;
            echo "FOUND & REMOVED in: " . basename($file) . "\n";
            echo "  Pattern: " . str_replace(["\n","\r"], ["\\n","\\r"], $pattern) . "\n";
        }
    }

    // Also use regex for any whitespace variation
    $newContent = preg_replace(
        '/(<body[^>]*>)\s*\.student-modal-footer\s*\{[^}]*\}\s*/',
        '$1' . "\n",
        $content
    );
    if ($newContent !== $content) {
        $content = $newContent;
        $changed = true;
        echo "REGEX REMOVED from body area: " . basename($file) . "\n";
    }

    // One more pass - just remove any standalone occurrence
    if (strpos($content, $strayText) !== false) {
        $content = str_replace($strayText, '', $content);
        $changed = true;
        echo "REMAINING stripped from: " . basename($file) . "\n";
    }

    if ($changed) {
        file_put_contents($file, $content);
        $totalFixed++;
        echo "✅ SAVED: " . basename($file) . "\n\n";
    } else {
        echo "OK (clean): " . basename($file) . "\n";
    }
}

echo "\n--- Files fixed: $totalFixed ---\n\n";

// Show what's currently in the body of New-Applications.php
$naFile = __DIR__ . '/Admission/Modules/New-Applications.php';
if (file_exists($naFile)) {
    echo "=== New-Applications.php: lines near <body> ===\n";
    $lines = file($naFile);
    foreach ($lines as $i => $line) {
        $lineNum = $i + 1;
        if ($lineNum >= 285 && $lineNum <= 300) {
            echo "Line $lineNum: " . rtrim($line) . "\n";
        }
        // Also flag any suspicious line
        if (stripos($line, 'student-modal-footer') !== false || 
            stripos($line, 'padding: 20px 32px') !== false) {
            echo "!!! SUSPICIOUS Line $lineNum: " . rtrim($line) . "\n";
        }
    }
}

// Clear OPCache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "\n✅ OPCache cleared!\n";
}

echo "\n=== DONE! Ctrl+Shift+R to reload. Delete this file after! ===\n";
?>
