<?php
/**
 * DIRECT FIX: Remove stray CSS from New-Applications.php on the live server
 * DELETE THIS FILE after running!
 */
header('Content-Type: text/plain; charset=utf-8');
echo "=== DIRECT FIX SCRIPT ===\n\n";

$file = __DIR__ . '/Admission/Modules/New-Applications.php';

if (!file_exists($file)) {
    die("❌ File not found: $file\n");
}

// Read file content
$content = file_get_contents($file);
$original = $content;
$originalLength = strlen($original);

echo "📄 File: New-Applications.php\n";
echo "📏 Original size: $originalLength bytes\n\n";

// All possible variations of the stray text
$strayVariants = [
    ".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }",
    ".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content:flex-end; background: #f8fafc; }",
    ".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc;  }",
    " .student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }",
    "\n.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }",
];

$found = false;
foreach ($strayVariants as $stray) {
    if (strpos($content, $stray) !== false) {
        $content = str_replace($stray, '', $content);
        $found = true;
        echo "✅ FOUND AND REMOVED stray CSS variant!\n";
    }
}

// Also use regex to catch any whitespace variations
$before = $content;
$content = preg_replace('/\.student-modal-footer\s*\{\s*padding:\s*20px\s*32px;\s*border-top:[^}]+\}/', '', $content);
if ($content !== $before) {
    $found = true;
    echo "✅ REMOVED stray CSS via regex!\n";
}

if (!$found) {
    echo "ℹ️ No stray CSS found in New-Applications.php local content.\n";
    echo "   The issue might be in another included file.\n\n";
    
    // Check all included files
    $otherFiles = [
        __DIR__ . '/Admission/Components/Sidebar.php',
        __DIR__ . '/Admission/Components/header.php',
        __DIR__ . '/Admission/Components/GlobalScripts.php',
        __DIR__ . '/Admission/Components/GlobalModal.php',
    ];
    
    echo "🔍 Checking included files...\n";
    foreach ($otherFiles as $f) {
        if (!file_exists($f)) continue;
        $fc = file_get_contents($f);
        if (strpos($fc, 'student-modal-footer') !== false || 
            strpos($fc, 'padding: 20px 32px') !== false ||
            strpos($fc, 'f8fafc') !== false) {
            echo "🚨 FOUND in: " . basename($f) . "\n";
            
            // Remove from this file too
            $fc = preg_replace('/\.student-modal-footer\s*\{\s*padding:\s*20px\s*32px;\s*border-top:[^}]+\}/', '', $fc);
            $fc = str_replace('.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }', '', $fc);
            file_put_contents($f, $fc);
            echo "✅ REMOVED from: " . basename($f) . "\n";
        } else {
            echo "✅ Clean: " . basename($f) . "\n";
        }
    }
}

// Save if changed
if ($content !== $original) {
    file_put_contents($file, $content);
    $newLength = strlen($content);
    echo "\n✅ File saved! New size: $newLength bytes (removed " . ($originalLength - $newLength) . " bytes)\n";
} else {
    echo "\nℹ️ New-Applications.php unchanged.\n";
}

// Clear OPCache
echo "\n=== Clearing OPCache ===\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPCache reset done!\n";
} else {
    echo "⚠️ opcache_reset not available\n";
}

// Show what's in the body area of New-Applications.php right now
echo "\n=== Current body area of New-Applications.php ===\n";
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (stripos($line, 'student-modal-footer') !== false || 
        stripos($line, '<body') !== false ||
        stripos($line, 'f8fafc') !== false ||
        stripos($line, 'edf2f7') !== false) {
        echo "Line " . ($i+1) . ": " . trim($line) . "\n";
    }
}

echo "\n=== DONE! Now: Ctrl+Shift+R to reload ===\n";
echo "⚠️ DELETE this file (fix_now.php) after use!\n";
