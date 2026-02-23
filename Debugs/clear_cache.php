<?php
/**
 * Nuclear Cache Buster + Stray CSS Remover
 * Run this ONCE on the server, then DELETE it.
 */
header('Content-Type: text/plain; charset=utf-8');
$base = __DIR__;

// ============================================================
// 1. REMOVE STRAY CSS TEXT from ALL PHP files in Admission/
// ============================================================
$strayPatterns = [
    '.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }',
    '.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content:flex-end; background: #f8fafc; }',
];

$scanDirs = [
    $base . '/Admission/Modules',
    $base . '/Admission/Components',
    $base . '/Admission',
];

echo "=== STEP 1: Scanning & Cleaning Stray CSS ===\n\n";
foreach ($scanDirs as $dir) {
    if (!is_dir($dir)) continue;
    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $original = $content;
        foreach ($strayPatterns as $stray) {
            $content = str_replace($stray, '', $content);
        }
        if ($content !== $original) {
            file_put_contents($file, $content);
            echo "✅ CLEANED stray CSS from: " . basename($file) . "\n";
        }
    }
}
echo "✅ Scan complete.\n\n";

// ============================================================
// 2. OPCACHE FULL RESET
// ============================================================
echo "=== STEP 2: Clearing OPCache ===\n\n";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✅ OPCache full reset: SUCCESS\n";
    } else {
        echo "⚠️ OPCache reset called but returned false\n";
    }
} else {
    echo "⚠️ opcache_reset() not available on this server\n";
}

// ============================================================
// 3. TOUCH ALL KEY FILES (force mtime update)
// ============================================================
echo "\n=== STEP 3: Touching Key Files ===\n\n";
$keyFiles = [
    $base . '/Admission/Components/Sidebar.php',
    $base . '/Admission/Components/header.php',
    $base . '/Admission/Components/GlobalScripts.php',
    $base . '/Admission/Modules/New-Applications.php',
    $base . '/Admission/Modules/Student-Requirements.php',
];
foreach ($keyFiles as $file) {
    if (file_exists($file)) {
        // Invalidate individual file in opcache
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($file, true);
        }
        touch($file);
        echo "✅ Touched: " . basename($file) . "\n";
    } else {
        echo "❌ Not found: " . basename($file) . "\n";
    }
}

echo "\n===========================================\n";
echo "✅ ALL DONE! Now do:\n";
echo "1. Hard reload: Ctrl + Shift + R\n";
echo "2. DELETE this file from the server!\n";
echo "===========================================\n";
